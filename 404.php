<?php
//this is a copy of the 404 file in the root folder.
// this version is not used and is for reference only
require_once(dirname(dirname(__FILE__)).'../../config.php');
global $CFG, $DB;

$theerrorr = $_SERVER['REQUEST_URI'];//PICK UP THE ERROR FROM THE URL
$theerrorr = substr($theerrorr,1);//STRIP OUT THE FORWARD SLASH
	
$sql = "SELECT url as theurl FROM {customurls} WHERE custom_name = ?";
$result_customurls = $DB->get_records_sql($sql, array($theerrorr)); 

foreach($result_customurls as $k=>$v){
	$thelocation = $v->theurl;
	if (!strstr($thelocation, "http")) {
		$thelocation = 'http://'.$thelocation;
		echo $thelocation;
	}
	
	if($k->theurl != NULL){
		$thelocation = $row['theurl'];
		if (!strstr($thelocation, "http")) {
			$thelocation = 'http://'.$thelocation;
		}	
		header('Location:'.$thelocation);//IF FOUND IN THE CUSTOM URL TABLE REDIRECT
	}
	
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('embedded');
$PAGE->set_title("Object Not Found");
$PAGE->set_heading("Object Not Found!");
$PAGE->set_url('/404.php');
//$PAGE->set_course($SITE);

echo $OUTPUT->header();	

echo '	<h1>Object not found!</h1>
		<p> The requested URL was not found on this server. <br /><br /> 

			If you entered the URL manually please check your
			spelling and try again. </p>
			
		<p> If you think this is a server error, please contact
		the <a href="mailto:ltu@solent.ac.uk">webmaster</a>.</p>

		<h2>Error 404</h2>
		<address>
			<a href="/">'.$CFG->wwwroot.'.solent.ac.uk</a><br />

			<span>
			'.date("m/d/Y h:i:s a", time()).'
			<br />
			Apache/2.2.12 (Linux/SUSE)</span>
		</address>
		<script>
		function goBack() {
			window.history.back()
		}
		</script>
		<button onclick="goBack()">Go Back</button>';
echo $OUTPUT->footer();
?>