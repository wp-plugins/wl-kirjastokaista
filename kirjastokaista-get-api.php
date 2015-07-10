<?php
$kirjastokaista_url = $_GET['kirjastokaista_url'];
if (isset($_GET['categories'])) {
	$categories_json = file_get_contents('http://'.$kirjastokaista_url.'/?output_categories_with_languages');
	echo utf8_encode($categories_json);
}