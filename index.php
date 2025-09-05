<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

$kodeMap = file_exists("kode_user.json") ? json_decode(file_get_contents("kode_user.json"), true) : [];

if (!isset($_SESSION["username"]) && isset($_POST["kode"])) {
  $kode = trim($_POST["kode"]);
  if (array_key_exists($kode, $kodeMap)) {
    $_SESSION["username"] = $kodeMap[$kode];
  } else {
    $error = "nice try :(";
  }
}

if (!isset($_SESSION["username"])) {
  echo '<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Masukkan Kode</title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
  <div class="chat-container" style="text-align:center;">
    <h2>Login</h2>
    <form method="POST" class="chat-form">
      <input type="text" name="kode" placeholder="..." required>
      <button type="submit">Login</button>
    </form>';
  if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
  }
  echo '</div></body></html>';
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
  $msg       = strip_tags($_POST["message"]);
  $user      = $_SESSION["username"];
  $reply     = $_POST["reply_to"] ?? "";
  $timestamp = date("Y-m-d H:i:s");
  $id        = uniqid();

  file_put_contents("messages.txt", "$id|$timestamp|$user|$msg|$reply\n", FILE_APPEND);
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

$messages = file_exists("messages.txt") ? file("messages.txt") : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>ours</title>
  <link rel="stylesheet" href="style/style.css">
  <script src="style/script.js" defer></script>
</head>
<body>
  <div class="chat-container">
    <div class="chat-window" id="chatWindow">
      <?php
      $lookup = [];

      foreach ($messages as $line):
        $parts = explode("|", trim($line));

        if (count($parts) === 4) {
          $id        = md5($parts[0] . $parts[1] . $parts[2]);
          $timestamp = $parts[0];
          $user      = $parts[1];
          $text      = $parts[2];
          $reply     = $parts[3];
        } elseif (count($parts) >= 5) {
          [$id, $timestamp, $user, $text, $reply] = $parts;
        } else {
          continue;
        }

        $lookup[$id] = $text;
      ?>
        <div class="message">
          <?php if ($reply && isset($lookup[$reply])): ?>
            <div class="reply-ref">
              <?php
                $quoted = $lookup[$reply];
                if (strpos($quoted, "[img]") === 0) {
                  $src = substr($quoted, 5);
                  echo "<div><em>Balasan ke gambar:</em><br><img src='" . htmlspecialchars($src) . "' class='chat-image reply-thumb'></div>";
                } elseif (strpos($quoted, "[audio]") === 0) {
                  $src = "upload/voice/" . basename(substr($quoted, 7));
                  echo "<em>Balasan ke voice:</em><br><audio controls src='" . htmlspecialchars($src) . "' class='chat-audio'></audio>";
                } elseif (strpos($quoted, "[video]") === 0) {
                  $src = "upload/video/" . basename(substr($quoted, 7));
                  echo "<em>Balasan ke video:</em><br><video controls src='" . htmlspecialchars($src) . "' class='chat-video reply-thumb'></video>";
                } else {
                  echo "<em>Balasan ke: \"" . htmlspecialchars($quoted) . "\"</em>";
                }
              ?>
            </div>
          <?php endif; ?>

          <strong><?= htmlspecialchars($timestamp) ?> - <?= htmlspecialchars($user) ?>:</strong>
          <?php
            if (strpos($text, "[img]") === 0) {
              $src = substr($text, 5);
              echo "<img src='" . htmlspecialchars($src) . "' class='chat-image' onclick='window.open(\"$src\")'>";
            } elseif (strpos($text, "[audio]") === 0) {
              $src = "upload/voice/" . basename(substr($text, 7));
              echo "<audio controls src='" . htmlspecialchars($src) . "' class='chat-audio'></audio>";
            } elseif (strpos($text, "[video]") === 0) {
              $src = "upload/video/" . basename(substr($text, 7));
              echo "<video controls src='" . htmlspecialchars($src) . "' class='chat-video'></video>";
            } else {
              echo htmlspecialchars($text);
            }
          ?>
          <button onclick="setReply('<?= htmlspecialchars($id) ?>', '<?= htmlspecialchars($text) ?>')">Reply</button>
        </div>
      <?php endforeach; ?>
    </div>

    <div id="replyPreview" style="display:none; margin:10px 0; padding:5px; border-left:3px solid #ccc;"></div>

    <form method="POST" class="chat-form" action="index.php">
      <input type="text" name="message" id="messageInput" placeholder="..." autocomplete="off" required data-username="<?= htmlspecialchars($_SESSION['username']) ?>">
      <input type="hidden" name="reply_to" id="replyToInput">
      <button type="submit">Send</button>
    </form>

    <form method="POST" action="upload.php" enctype="multipart/form-data" class="chat-form">
      <input type="file" name="file" id="fileInput" accept="image/*,video/*,audio/*" required>
      <input type="hidden" name="reply_to" id="replyToInputFile">
      <button type="submit">Upload File</button>
    </form>

    <div id="userStatus">
      <div class="online-users">
        <p>Loading...</p>
      </div>
    </div>

    <div class="notification-control">
      <form method="POST" action="trigger_send.php">
        <button type="submit" class="notif-button">Send notification</button>
      </form>
    </div>

    <div class="gallery-control">
      <form method="GET" action="gallery.php">
        <button type="submit" class="gallery-button">Gallery</button>
      </form>
    </div>
  </div>
</body>
</html>