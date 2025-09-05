<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if (!isset($_SESSION["username"])) {
  header("Location: index.php");
  exit;
}

function scanMediaFolder($folder, $type) {
  $files = glob($folder . "*");
  $media = [];

  foreach ($files as $file) {
    if (is_file($file)) {
      $media[] = [
        "src" => $file,
        "type" => $type,
        "user" => "unknown",
        "time" => date("Y-m-d H:i:s", filemtime($file))
      ];
    }
  }

  return $media;
}

$messages = file_exists("messages.txt") ? file("messages.txt") : [];
$media = [];

foreach ($messages as $line) {
  $parts = explode("|", trim($line));
  if (count($parts) >= 3) {
    [$timestamp, $user, $text] = array_pad($parts, 4, "");

    if (strpos($text, "[img]") === 0) {
      $src = "upload/" . substr($text, 5);
      $type = "image";
    } elseif (strpos($text, "[video]") === 0) {
      $src = "upload/video/" . substr($text, 7);
      $type = "video";
    } elseif (strpos($text, "[audio]") === 0) {
      $src = "upload/voice/" . substr($text, 7);
      $type = "audio";
    } else {
      continue;
    }

    if (file_exists($src)) {
      $media[] = [
        "src" => $src,
        "type" => $type,
        "user" => $user,
        "time" => $timestamp
      ];
    }
  }
}

$media = array_merge(
  $media,
  scanMediaFolder("upload/", "image"),
  scanMediaFolder("upload/video/", "video"),
  scanMediaFolder("upload/voice/", "audio")
);

$unique = [];
$finalMedia = [];

foreach ($media as $item) {
  if (!in_array($item['src'], $unique)) {
    $unique[] = $item['src'];
    $finalMedia[] = $item;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>FILE KITA</title>
  <link rel="stylesheet" href="style/style2.css">
</head>
<body>
  <div class="chat-container">
    <h2>FILEEEE</h2>
    <div class="gallery-grid">
      <?php foreach ($finalMedia as $item): ?>
        <div>
          <?php if ($item['type'] === 'image'): ?>
            <img src="<?= htmlspecialchars($item['src']) ?>" alt="Foto" onclick="window.open('<?= htmlspecialchars($item['src']) ?>', '_blank')">
          <?php elseif ($item['type'] === 'video'): ?>
            <video controls class="chat-video">
              <source src="<?= htmlspecialchars($item['src']) ?>" type="video/mp4">
              Browsermu tidak mendukung video.
            </video>
          <?php elseif ($item['type'] === 'audio'): ?>
            <audio controls class="chat-audio">
              <source src="<?= htmlspecialchars($item['src']) ?>" type="audio/mp4">
              Browsermu tidak mendukung audio.
            </audio>
          <?php endif; ?>
          <div>
            <?= htmlspecialchars($item['user']) ?> <br>
            <em><?= htmlspecialchars($item['time']) ?></em>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;">
      <a href="index.php">Back</a>
    </div>
  </div>
</body>
</html>