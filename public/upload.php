<?php

require_once __DIR__ . '/protected/config/config.php';
require_once __DIR__ . '/protected/utils.php';

$dir = authorize($_POST['password'], $_POST['user']);
if ($dir === false)
{
    dir('error,e-401');
}

if ( ! ((getimagesize($_FILES['image']['tmp_name'])) && $_FILES['image']['type'] == 'image/png' || $_FILES['image']['type'] == 'image/jpeg' || $_FILES['image']['type'] == 'image/gif')) {
    die('error,e-415');
}

if ($_FILES['image']['error'] > 0) {
    die('error,e-500');
}

$dir = __DIR__. $dir . '/';

saveImage($_FILES['image']['type'], $_FILES['image']['tmp_name']);

function generateNewHash($type)
{
    // Recursive functions are bad, especially when you can do them with a cycle.
    $an = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    do
    {
        $str = '';
        
        for ($i = 0; $i < 5; $i++) {
            $str .= substr($an, rand(0, strlen($an) - 1), 1);
        }
    }
    while (!isUnique($str, $type, __DIR__));
    return $str;
}

function saveImage($mimeType, $tempName)
{
    global $dir;

    switch ($mimeType) {
        case 'image/png':   $type = 'png'; break;
        case 'image/jpeg':  $type = 'jpeg'; break;
        case 'image/gif':   $type = 'gif'; break;

        default: die('error,e-415');
    }

    $hash = generateNewHash($type);
    $dir = $dir.$type;
    
    // Ensure folder exists.
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    
    if (move_uploaded_file($tempName, $dir . "/$hash.$type")) {
        die('success,' . (RAW_IMAGE_LINK ? $dir . "/$hash.$type" : ($type == 'png' ? '' : substr($type, 0, 1) . '/') . "$hash" . (IMAGE_EXTENSION ? ".$type" : '')));
    }

    die('error,e-500x');
}
