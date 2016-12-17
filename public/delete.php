<?php

require_once __DIR__ . '/protected/config/config.php';
require_once __DIR__ . '/protected/utils.php';

$dir = authorize($_POST['password'], $_POST['user']);
if ( $dir === false )
{
  die('Error: Unathorized');
}

if (!isset($_POST['file']) )
{
  die('Error: File was not set!');
}

if (strpos($_POST['file'], '..') !== false)
{
  die('Error: Invalid file!');
}

$dir = __DIR__ . $dir . '/'. $_POST['file'];
if (file_exists($dir) && !is_dir($dir))
{
  unlink($dir);
  die('success');
}
die('error,e-400x');