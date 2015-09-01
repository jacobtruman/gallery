<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("Thumbnailer.class.php");

$params = json_decode(base64_decode($_REQUEST['p']), true);
if(isset($_REQUEST['v']) && $_REQUEST['v'] == 1) {
	var_dump($params);
	exit;
}

$file = $params['file'];
$fileDir = '/mine/Pictures';
if(isset($params['type']) && $params['type'] == "thumb") {
	$filePath = $fileDir."/".$file;
	$thumbnailer = new Thumbnailer($fileDir);
	if(!$thumbnailer->generateThumb($filePath)) {
		echo "Failed to generate thumbnail".PHP_EOL;
		exit;
	}
	$fileDir .= "/thumbnails";
}

$filePath = $fileDir."/".$file;
if (file_exists($filePath)) {
	// Note: You should probably do some more checks 
	// on the filetype, size, etc.
	$contents = file_get_contents($filePath);

	// Note: You should probably implement some kind 
	// of check on filetype
	header('Content-type: image/jpeg');

	echo $contents;
} else {
	echo "{$filePath} does not exist...";
	exit;
}

?>
