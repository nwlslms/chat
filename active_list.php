<?php

$file = "active_users.json";
$users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$now = time();
$active = [];

foreach ($users as $name => $timestamp) {
  if ($now - $timestamp <= 10) {
    $active[] = htmlspecialchars($name);
  }
}

echo implode(", ", $active);
