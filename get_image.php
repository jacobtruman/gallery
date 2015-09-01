<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);

$params = json_decode(base64_decode($_REQUEST['p']), true);
if(isset($_REQUEST['v']) && $_REQUEST['v'] == 1) {
	var_dump($params);
	exit;
}

$file = $params['file'];
$fileDir = '/mine/Pictures';
if(isset($params['type']) && $params['type'] == "thumb") {
	$filePath = $fileDir . $file;
	$ext = pathinfo($filePath, PATHINFO_EXTENSION);
	$fileDir .= "/thumbnails";
	$thumbnail = $fileDir . $file;
	$file_parts = explode("/", $thumbnail);
	unset($file_parts[count($file_parts) - 1]);
	$thumb_dir = implode("/", $file_parts);

	if(!file_exists($thumb_dir)) {
		mkdir($thumb_dir, 0777, true);
	}
	if(!file_exists($thumbnail)) {
		if(file_exists($filePath)) {
			//echo "Generating thumbnail...{$thumbnail} for {$filePath}<br />".PHP_EOL;
			$imgSize = getimagesize($filePath);
			list($width, $height) = $imgSize;

			$new_height = 150;
			$new_width = $width / ($height / $new_height);

			if (in_array(strtolower($ext), array("jpg", "jpeg"))) {
				$methods = array("imagecreatefrom" => "imagecreatefromjpeg", "image" => "imagejpeg");
				$quality = 100;
			} elseif (strtolower($ext) == "png") {
				$methods = array("imagecreatefrom" => "imagecreatefrompng", "image" => "imagepng");
				$quality = 9;
			} elseif (strtolower($ext) == "gif") {
				$methods = array("imagecreatefrom" => "imagecreatefromgif", "image" => "imagegif");
				$quality = 100;
			}

			// Load the images
			$thumb = imagecreatetruecolor($new_width, $new_height);
			if(!$thumb) {
				echo "imagecreatetruecolor failed<br />".PHP_EOL;
				exit;
			}
			$source = $methods["imagecreatefrom"]($filePath);
			if(!$source) {
				echo "{$methods["imagecreatefrom"]} failed<br />".PHP_EOL;
				exit;
			}

			// Resize the $thumb image.
			if(imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
				// Save the new file to the location specified by $thumbnail
				if (!$methods["image"]($thumb, $thumbnail, $quality)) {
					echo "{$methods["image"]} FAILED to generate thumbnail...{$thumbnail}<br />" . PHP_EOL;
					exit;
				}
			} else {
				echo "imagecopyresized failed<br />".PHP_EOL;
				exit;
			}
		} else {
			echo $filePath." does not exist...";
			exit;
		}
	}
}

if (file_exists($fileDir . $file)) {
	// Note: You should probably do some more checks 
	// on the filetype, size, etc.
	$contents = file_get_contents($fileDir . $file);

	// Note: You should probably implement some kind 
	// of check on filetype
	header('Content-type: image/jpeg');

	echo $contents;
} else {
	echo $fileDir . $file." does not exist...";
	exit;
}

?>
