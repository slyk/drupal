<?php
/**
 * used to load external resources, because sfw flash files cant do this because of policy issues
 * need to add crossdomain.xml to all websites root whre we need to download files or use this proxy
 */

$srv = $_GET['srv'];
$path= $_GET['path'];

//if($srv=='tpsadminfuncs') $srv = 'http://srv1.toopro.org:3088/'; else die();
switch ($srv) {
    case 'tpsadminfuncs': $srv = 'http://adm.toopro.org:3088/'; break;
    case 'nfeya.com'    : $srv = 'https://nfeya.com/'; break;
    default: die();
}

$url = $srv . $path;
echo file_get_contents($url);