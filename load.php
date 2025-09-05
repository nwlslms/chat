<?php
$messages = file_exists("messages.txt") ? file("messages.txt") : [];
$lookup = [];
foreach ($messages as $line) {
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
}
foreach ($messages as $line) {
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

  if (empty($text)) continue;

  echo "<div class='message'>";
  if ($reply && isset($lookup[$reply])) {
    $quoted = $lookup[$reply];
    echo "<div class='reply-ref'>";
    if (strpos($quoted, "[img]") === 0) {
      $src = "upload/" . substr($quoted, 5);
      if (file_exists($src)) {
        echo "<div><em>Reply to image:</em><br><img src='" . htmlspecialchars($src) . "' class='chat-image reply-thumb'></div>";
      } else {
        echo "<div class='broken-image'>🧯 Image not found: " . htmlspecialchars($src) . "</div>";
      }
    } elseif (strpos($quoted, "[audio]") === 0) {
      $src = "upload/voice/" . basename(substr($quoted, 7));
      echo "<em>Reply to voice:</em><br><audio controls src='" . htmlspecialchars($src) . "' class='chat-audio'></audio>";
    } elseif (strpos($quoted, "[video]") === 0) {
      $src = "upload/video/" . basename(substr($quoted, 7));
      echo "<em>Reply to video:</em><br><video controls src='" . htmlspecialchars($src) . "' class='chat-video'></video>";
    } else {
      echo "<blockquote>\"" . htmlspecialchars($quoted) . "\"</blockquote>";
    }
    echo "</div>";
  }
  echo "<strong>" . htmlspecialchars($timestamp) . " - " . htmlspecialchars($user) . ":</strong> ";

  if (strpos($text, "[img]") === 0) {
    $src = "upload/" . substr($text, 5);
    if (file_exists($src)) {
      echo "<img src='" . htmlspecialchars($src) . "' class='chat-image' onclick='window.open(\"$src\", \"_blank\")'>";
    } else {
      echo "<div class='broken-image'>🧯 Image not found: " . htmlspecialchars($src) . "</div>";
    }
  } elseif (strpos($text, "[audio]") === 0) {
    $src = "upload/voice/" . basename(substr($text, 7));
    echo "<audio controls src='" . htmlspecialchars($src) . "' class='chat-audio'></audio>";
  } elseif (strpos($text, "[video]") === 0) {
    $src = "upload/video/" . basename(substr($text, 7));
    echo "<video controls src='" . htmlspecialchars($src) . "' class='chat-video'></video>";
  } else {
    echo htmlspecialchars($text);
  }

  echo " <button onclick=\"setReply('" . htmlspecialchars($id) . "', '" . htmlspecialchars($text) . "')\">Reply</button>";
  echo "</div>";
}
?>