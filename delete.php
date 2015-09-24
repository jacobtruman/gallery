<?php
if($_REQUEST['debug']) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

$trash_dir = "/mine/ImageTrash";
if(!file_exists($trash_dir)) {
	mkdir($trash_dir, 0777);
}

$result = array();
if(isset($_REQUEST['base64file'])) {
	$result['base64file'] = $_REQUEST['base64file'];
	$result['source_file'] = base64_decode($_REQUEST['base64file']);
	$result['dest_file'] = $trash_dir.$result['source_file'];
	$dest_dir = dirname($result['dest_file']);
	if(!file_exists($dest_dir)) {
		mkdir($dest_dir, 0777, true);
	}
	$result['status'] = rename($result['source_file'], $result['dest_file']);
} else {
	$result['status'] = false;
}

echo json_encode($result);
?>