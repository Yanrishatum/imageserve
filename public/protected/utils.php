<?php

require_once __DIR__.'/config/config.php';
// Backwards compatability with older config.php
if (!defined("MULTIUSER")) define("MULTIUSER", false);

function authorize($passkey, $user)
{
  global $users;
  if (!isset($passkey)) return false;
  if (MULTIUSER)
  {
    if (!isset($user) || empty($user)) $user = 'default';
    
    $user = $users[$user];
    
    if (isset($user) && $user["passkey"] === $passkey)
    {
      return $user['path'];
    }
    return false;
  }
  else
  {
    return $passkey === PASSKEY ? '/images' : false;
  }
}

// Search across all users
function isUnique($hash, $type, $dir)
{
  global $users;
  if (MULTIUSER)
  {
    foreach ($users as $user)
    {
      $userDir = $dir.$user["path"]."/$type/$hash.$type";
      if (file_exists($userDir)) return false;
    }
    return true;
  }
  else
  {
    return !file_exists("$dir/images/$type/$hash.$type");
  }
}

function findImage($dir, $file, $type)
{
  global $users;
  if (MULTIUSER)
  {
    foreach ($users as $user)
    {
      $userDir = $dir.$user["path"]."/$type/$file.$type";
      if (file_exists($userDir)) return $userDir;
    }
    return false;
  }
  else
  {
    $dir = "$dir/images/$type/$file.$type";
    return file_exists($dir) ? $dir : false;
  }
}