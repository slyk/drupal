<?php
/**
 * used to load external resources, because sfw flash files cant do this because of policy issues
 * need to add crossdomain.xml to all websites root whre we need to download files or use this proxy
 */

$srv  = $_GET['srv'];
$path = $_GET['path'];

//if($srv=='tpsadminfuncs') $srv = 'http://srv1.toopro.org:3088/'; else die();
switch ($srv) {
    case 'tpsadminfuncs': $srv = 'http://adm.toopro.org:3088/'; break;
    case 'nfeya.com'    : $srv = 'https://nfeya.com/'; break;
    case 'daemon'       : $srv = 'http://localhost:3100/'; break; //example: http://petr.tps.my/proxy.php?srv=daemon&path=api/pos-terminal/ping
    default: die();
}
$url = $srv . $path;

// Get the headers from the destination URL (this makes double request, so only for nfeya.com image requests)
if(strpos($url, 'nfeya.com') !== FALSE) { //for images we need headeres, for json not
    $responseHeaders = get_headers($url, 1); //print_r($responseHeaders);die();
    foreach ($responseHeaders as $name => $value) {
        if (is_array($value)) foreach ($value as $item) header("$name: $item", false);
        else header("$name: $value", false);
    }

    //check if its webp image then it will echo jpg and return true
    $jpegConverted = checkWebp($responseHeaders, $url); if($jpegConverted) die($jpegConverted);
}

// Finally, output the content
$timeout = 10; if($_GET['srv']=='daemon') $timeout = 120; //for daemon we need more time
$context = stream_context_create([
    'http' => [ 'timeout' => $timeout ]
]);
echo file_get_contents($url, false, $context);

///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

function checkWebp($headers, $url) {
  //if its not webp image, return false
  if(!isset($headers['Content-Type']) || $headers['Content-Type'] != 'image/webp') return false;

  //for webp we need to convert it to jpg
  $img = imagecreatefromwebp($url);

  //output to var
  ob_start();
  imagejpeg($img, null, 60);
  $content = ob_get_clean(); // Get the content of the output buffer
  imagedestroy($img);

  //remove old header and set new one
  header_remove();
  header_remove('Content-Type');
  header('Content-Type: image/jpeg');
  //add header with filename of the downloaded file
  //header('Content-Disposition: inline; filename="'.basename($url).'.jpg"');
  header('Content-Disposition: inline; filename=photo.jpeg');
  return $content;
}