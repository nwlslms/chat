<?php
session_start();

if (!isset($_SESSION["username"])) {
  exit("Not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
  $file      = $_FILES["file"];
  $reply     = $_POST["reply_to"] ?? "";
  $user      = $_SESSION["username"];
  $timestamp = date("Y-m-d H:i:s");
  $id        = uniqid();

  $mime = $file["type"];
  $ext  = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

  if (strpos($mime, "image/") === 0) {
    $folder = "upload/";
    $prefix = "[img]";
  } elseif (strpos($mime, "audio/") === 0 || in_array($ext, ["mp3", "wav", "ogg"])) {
    $folder = "upload/voice/";
    $prefix = "[audio]";
  } elseif (strpos($mime, "video/") === 0 || in_array($ext, ["mp4", "webm", "mov"])) {
    $folder = "upload/video/";
    $prefix = "[video]";
  } else {
    exit("Unsupported file type.");
  }

  if (!is_dir($folder)) mkdir($folder, 0755, true);

  $safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", basename($file["name"]));
  $path     = $folder . $safeName;

  if (move_uploaded_file($file["tmp_name"], $path)) {
    $msg = $prefix . $safeName;
    file_put_contents("messages.txt", "$id|$timestamp|$user|$msg|$reply\n", FILE_APPEND);
    file_put_contents("typing.txt", "");
    header("Location: index.php");
    exit;
  } else {
    exit("Failed.");
  }
}
?>