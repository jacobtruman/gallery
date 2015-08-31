<?php

define(BASE_DIR,  "/mine/backup/Pictures");
define(THUMB_BASE, BASE_DIR."/thumbnails");

processDir(BASE_DIR);

function processDir($dir) {
	$img_exts = array('jpg', 'png');
	$files = glob($dir."/*");

	foreach($files as $file) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if(is_dir($file) && $file !== THUMB_BASE) {
			processDir($file);
		} elseif(in_array($ext, $img_exts)) {
			generateThumb($file);
		}
	}
}

function getThumbDir($file) {
	$file_info = pathinfo($file);
	$thumb_dir = str_replace(BASE_DIR, THUMB_BASE, $file_info['dirname']);
	if(!file_exists($thumb_dir)) {
		mkdir($thumb_dir, 0777, true);
	}
	return $thumb_dir;
}

function generateThumb($file) {
	echo $file."\n";
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	$thumb_dir = getThumbDir($file);
	$file_parts = explode("/", $file);
	$file_name = end($file_parts);

	$thumbnail = $thumb_dir."/".$file_name;
	echo $thumbnail."\n";
	if(!file_exists($thumbnail)) {
		list($width, $height) = getimagesize($file);

		$new_height = 150;
		$new_width = $width / ($height / $new_height);

		if(in_array($ext, array("jpg", "jpeg"))) {
			$methods = array("imagecreatefrom"=>"imagecreatefromjpeg", "image"=>"imagejpeg");
			$quality = 100;
		} elseif($ext == "png") {
			$methods = array("imagecreatefrom"=>"imagecreatefrompng", "image"=>"imagepng");
			$quality = 9;
		}

		// Load the images
		$thumb = imagecreatetruecolor($new_width, $new_height);
		$source = $methods["imagecreatefrom"]($file);

		// Resize the $thumb image.
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		// Save the new file to the location specified by $thumbnail
		$methods["image"]($thumb, $thumbnail, $quality);
	}
}
?>