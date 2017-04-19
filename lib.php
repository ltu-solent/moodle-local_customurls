<?php

function url_exists($url){

	$handle   = curl_init($url);

	if (false === $handle){
		throw new Exception('Fail to start Curl session');
	}

	curl_setopt($handle, CURLOPT_HEADER, false);
	curl_setopt($handle, CURLOPT_NOBODY, true);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);

	// grab Url
	$connectable = curl_exec($handle);

	// close Curl resource, and free up system resources
	curl_close($handle);   
	return $connectable;
}
?>