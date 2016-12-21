<?php
  require_once __DIR__ . '/protected/config/config.php';
  require_once __DIR__ . '/protected/utils.php';
  $protocol = isset($_SERVER['https']) && $_SERVER['https'] != "off" ? "https" : "http";
  
  // Backwards compatability
  if (!defined("PUBLIC_LIST")) define("PUBLIC_LIST", false);
  if (!defined("IMAGES_PER_PAGE")) define("IMAGES_PER_PAGE", 20);
  
  $havePassword = isset($_GET["password"]);
  $password = $havePassword ? $_GET["password"] : "";
  $user = isset($_GET["user"]) ? $_GET["user"] : "default";
  
  $dir = '/images';
  if (MULTIUSER || !PUBLIC_LIST)
  {
    $dir = authorize($password, $user);
  }
  
  if ($dir === false)
  {
    header('HTTP/1.0 401 Unauthorized');
    include_once __DIR__ . '/protected/templates/unathorized.phtml';
    die();
  }
  
  $time = microtime();
  $time = explode(' ', $time);
  $time = $time[1] + $time[0];
  $start = $time;
  
  // Yay, stackoverflow
  function scan_recursive($dir, &$files, $prefix)
  {
    $ignored = array('.', ".gitkeep", '..', '.htaccess');
    
    foreach(scandir($dir) as $file)
    {
      if (in_array($file, $ignored)) continue;
      $subDir = $dir.'/'.$file;
      if (is_dir($subDir))
      {
        scan_recursive($subDir, $files, empty($prefix) ? $file.'/' : $prefix.$file.'/');
      }
      else
      {
        $files[$prefix.$file] = filemtime($dir . '/' . $file);
      }
    }
  }
  
  function scan_dir($dir)
  {
    $files = array();
    scan_recursive($dir, $files, '');
    
    arsort($files);
    $files = array_keys($files);
    
    return ($files) ? $files : false;
  }
  
  $files = scan_dir(__DIR__ . $dir);
  $page = isset($_GET["page"]) ? intval($_GET["page"]) - 1 : 0;
  
  $fileCount = count($files);
  $pages = ceil($fileCount / IMAGES_PER_PAGE);
  $offset = $page * IMAGES_PER_PAGE;
  
  $limit = min($fileCount - $offset, IMAGES_PER_PAGE);
  
  require_once __DIR__ . '/protected/templates/list.phtml';
  
  
?>