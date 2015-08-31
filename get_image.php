<?php

$params = json_decode(base64_decode($_REQUEST['p']), true);

$file = $params['file'];
$type = $params['type'];
$fileDir = '/mine/Pictures/';
if($type == "thumb") {
	$fileDir .= "thumbnails/";
}

if (file_exists($fileDir . $file)) {
	// Note: You should probably do some more checks 
	// on the filetype, size, etc.
	$contents = file_get_contents($fileDir . $file);

	// Note: You should probably implement some kind 
	// of check on filetype
	header('Content-type: image/jpeg');

	echo $contents;
}

?>
