<?php
require_once(dirname(dirname(__FILE__)).'../../config.php');
global $CFG, $DB, $USER;
require_capability('moodle/site:config', context_system::instance());
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Delete custom urls");
$PAGE->set_heading("Delete custom urls");
$PAGE->set_url($CFG->wwwroot.'/local/customurls/delete.php');
echo '<link rel="stylesheet" type="text/css" href="styles.css">';

echo $OUTPUT->header();
$id = $_GET['id'];

$result = $DB->get_records_sql('SELECT * FROM {customurls} WHERE id = ?', array($id));

foreach($result as $k=>$v){
	echo '
		<h2 class="main">Delete Custom Url</h2>
		<p class="delete_alert">Are you sure you want to delete this entry? This can not be undone.</p>
		<div class="delete_box">
		<form name="form1" method="post" action="submit.php">
		<table class ="thetable">
		<tr><th>Notes</th><th>URL</th><th>Custom name</th></tr>
		  <tr>
			<td class ="tdinfo">'. $v->info .'</td>
			<td>'. $v->url .'</td>
			<td>'. $v->custom_name .'</td>
		  </tr>
		</table>
		<input name="id" type="hidden" id="id" value="'. $v->id .'">
		<div class="delete_buttons"><input type="submit" name="Submit" value="Confirm">
		<a href="edit.php"><input type="button" name="cancel" value="Cancel" /></a>
		</div>
		</form>
		</div>';
}

echo $OUTPUT->footer();
?>