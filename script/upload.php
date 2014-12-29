<?php
require 'config.php';

try {

	if( !isset($_FILES['photo']['error']) || 
		is_array($_FILES['photo']['error']) )
	{
		throw new RuntimeException("Invalid params.");

	}
	switch( $_FILES['photo']['error'])
	{
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException("No file sent.");
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FROM_SIZE:
			throw new RuntimeException("Exceeded filesize limit.");
		default:
			throw new RuntimeException("Unknown errors.");
	}
	$tmp_name = $_FILES['photo']['tmp_name'];

	/* TODO: check mime type
	 * Don't trust HTTP's mime type
	 */
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$ext = array_search(
		$finfo->file($tmp_name), 
		array(
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif'
		), 
		true
	);
	if( !$ext )
	{
		throw new RuntimeException("Mime type error.");
	}

	$target = sprintf("%s/%s.%s", $UPLOADTMP, sha1_file($tmp_name), $ext);
	if( !move_uploaded_file($tmp_name, $target) )
	{
		throw new RuntimeException("Fail to move uploaded file.");
	}

	echo "ok";
} catch (RuntimeException $e) {
	echo $e->getMessage();
}


?>
