<?php
require_once('app/app.php');
// The posted data, for reference
$file = $_FILES['file'];
$directory = $_POST['directory'];
$directory = filter_var($directory, FILTER_SANITIZE_STRING);  	

echo $upload->upload($file,$directory);
?>