<?php
require_once(dirname(dirname(__FILE__)).'../../config.php');
global $CFG, $DB, $USER;

$id=$_POST['id'];

// update data in mysql database
if (isset($_POST['id'])){
	$deletequery = $DB->delete_records('customurls', array('id'=>$id)); 
}

header('Location:edit.php');

?>