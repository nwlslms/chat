<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if (isset($_SESSION["username"])) {
  $user = $_SESSION["username"];
  $now = time();

  $file = "active_users.json";
  $users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

  $users[$user] = $now;

  file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
}
