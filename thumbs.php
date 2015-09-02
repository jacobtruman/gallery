<?php

define(BASE_DIR,  "/mine/Pictures");

processDir(BASE_DIR);

function processDir($dir) {
	$files = glob($dir."/*");

	foreach($files as $file) {
		$thumbnailer = new Thumbnailer(BASE_DIR);
		if(!$thumbnailer->generateThumb($file)) {
			echo "Failed to generate thumbnail".PHP_EOL;
			exit;
		}
	}
}
?>