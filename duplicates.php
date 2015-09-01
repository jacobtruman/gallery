<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 300);

require_once("ImgCompare.class.php");

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Gallery</title>

		<link href="css/site.css?v=0.9.9" rel="stylesheet" />
		<link href="css/magnific-popup.css?v=0.9.9" rel="stylesheet" />

		<script>
			//document.write('<script src=js/' + ('__proto__' in {} ? 'zepto' : 'jquery') + '.min.js><\/script>')
		</script>
		<script src="js/jquery.js"></script>
		<script src="js/jquery.magnific-popup.js?v=0.9.9"></script>
		<script src="js/jquery.lazyload.js" type="text/javascript"></script>
	</head>

<?php
$img_exts = array('jpg', 'jpeg', 'png', 'JPG', 'gif');
$base_dir = "/mine/Pictures";
$theDir = $base_dir;

if(isset($_REQUEST['dir'])) {
	$theDir .= "/".$_REQUEST['dir'];
}

$web_dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
$dir = $base_dir."/".$web_dir;

$imgCompare = new ImgCompare($dir, $img_exts, array("path"=>array($dir)), true);
$duplicates = $imgCompare->getDuplicates();

$thumb_base = $base_dir."/thumbnails";
$thumb_dir = $thumb_base."/".$web_dir;
if(!file_exists($thumb_dir)) {
	mkdir($thumb_dir, 0777, true);
}

//$files = glob($theDir."/*");
foreach($duplicates as $hash=>$files) {
	foreach ($files as $file) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if (is_dir($file) && $file !== $thumb_base) {
			$dir = str_replace($theDir . "/", "", $file);
			$dirParam = isset($_REQUEST['dir']) ? $_REQUEST['dir'] . "/" . $dir : $dir;
			echo "<a href='{$_SERVER['PHP_SELF']}?dir={$dirParam}'>{$dir}</a><br />".PHP_EOL;
		} elseif (in_array($ext, $img_exts)) {
			$imgs[$hash][] = $file;
		}
	}
}
?>
	<div class="popup-gallery">
		<?php
		if(isset($imgs) && count($imgs)) {
			foreach($imgs as $hash=>$files) {
				foreach ($files as $file) {
					$ext = pathinfo($file, PATHINFO_EXTENSION);

					$file_parts = explode("/", $file);
					$file_name = end($file_parts);

					$web_path = str_replace($base_dir, "", $file);

					$thumbnail = $thumb_dir . "/" . $file_name;

					$full_params = array("file" => $web_path);
					$thumb_params = $full_params;
					$thumb_params['type'] = "thumb";
					$title = str_replace("." . $ext, "", str_replace(array("`", "'"), ":", basename($file)));
					echo "<div style='float:left;'><a class='image-popup-no-margins 'title='{$title}' href='get_image.php?p=" . base64_encode(json_encode($full_params)) . "'><img src='get_image.php?p=" . base64_encode(json_encode($thumb_params)) . "'/></a><br /><input style='width: 400px;' onclick='this.select()' type='text' value=\"".addslashes($file)."\" /></div>".PHP_EOL;
				}
				echo "<br style='clear:both' />".PHP_EOL;
			}
		} else {
			echo "There are no duplicate images within '{$web_dir}'".PHP_EOL;
		}
		?>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.popup-gallery').magnificPopup({
				delegate: 'a',
				type: 'image',
				tLoading: 'Loading image #%curr%...',
				mainClass: 'mfp-img-mobile',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0,1] // Will preload 0 - before current, and 1 after the current image
				},
				image: {
					tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
					titleSrc: function(item) {
						return item.el.attr('title') + '<small>by Truman</small>';
					}
				}
			});
		});

		$(function() {
			$("img.lazy").lazyload();
		});
	</script>

</html>
