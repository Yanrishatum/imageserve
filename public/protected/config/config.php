<?php

//   _
//  (_)    A self-hosted ShareX image serving solution.
//   _ _ __ ___   __ _  __ _  ___  ___  ___ _ ____   _____
//  | | '_ ` _ \ / _` |/ _` |/ _ \/ __|/ _ \ '__\ \ / / _ \
//  | | | | | | | (_| | (_| |  __/\__ \  __/ |   \ V /  __/
//  |_|_| |_| |_|\__,_|\__, |\___||___/\___|_|    \_/ \___|
//                      __/ |
//                     |___/   created by github.com/aerouk

/* More information on all these values can be found on the wiki page. */
/* https://github.com/aerouk/imageserve/wiki/Configuration */

define('RAW_IMAGE', false);
define('RAW_IMAGE_LINK', false);
define('IMAGE_EXTENSION', false);

define('TWITTER_CARDS', true);

// If you're using this, make sure to put a forward slash before.
// E.g. "/imageserve" not "imageserve"
define('IMAGESERVE_DIR', '');
define('TWITTER_HANDLE', '@aerouk_');

define('APP_NAME', 'application name');
define('PASSKEY', 'password goes here');

// list.php
define('PUBLIC_LIST', false); // Allow image list access without password
define('IMAGES_PER_PAGE', 20);

// Multiuser support
define('MULTIUSER', false);
// Each user have an ID, access passkey and image storage path.
// 'ID' => array('passkey'=>'user passkey', 'path'=>'where to hold images')
// The 'default' used as a fallback user when no 'user' parameter present in request.
// As with IMAGESERVE_DIR - path should start with a forward slash, and end without one.
$users = array(
   // DO NOT delete the 'default' user.
  'default' => array(
    "passkey"=>PASSKEY,
    "path"=>"/images",
    "public_list"=>PUBLIC_LIST
  )
);
