<?php
$file = "active_users.json";
$users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$now = time();

$display = ["sil", "her"];
$statusHtml = "";

foreach ($display as $name) {
  $lastSeen = isset($users[$name]) ? $users[$name] : null;

  if ($lastSeen && ($now - $lastSeen <= 10)) {
    $statusHtml .= "<div>🔵 <strong>$name</strong> is online</div>";
  } elseif ($lastSeen) {
    $elapsed = $now - $lastSeen;
    $minutes = floor($elapsed / 60);
    $seconds = $elapsed % 60;
    $ago = ($minutes > 0 ? "$minutes minute(s) " : "") . "$seconds second(s) ago";
    $statusHtml .= "<div>🕒 <strong>$name</strong> was last seen: $ago</div>";
  } else {
    $statusHtml .= "<div>⚫️ <strong>$name</strong> has never been active</div>";
  }
}

echo $statusHtml;
?>