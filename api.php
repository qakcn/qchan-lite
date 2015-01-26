<?php
define('API_RUN',true);

require 'engine.php';

if(!isset($_SERVER['HTTP_REFERER']) && !isset($_GET['apikey'])) {
	header('HTTP/1.1 403 Forbidden');
	exit('API cannot be direct accessed!');
}

if(isset($_GET['gettoken']) && $_GET['gettoken']=='file') {
	$result = get_token('file');
}else if(isset($_GET['gettoken']) && $_GET['gettoken']=='url') {
	$result = get_token('url');
}else if(isset($_GET['type']) && $_GET['type']=='url') {
	$result = url_handler();
}else if(isset($_GET['type']) && $_GET['type']=='file') {
	$result = array_pop(file_handler());
}else {
	header('HTTP/1.1 400 Bad Request');
	exit('You must set "type" in the query string.');
}
header('Content-Type: application/json');
echo json_encode($result);

