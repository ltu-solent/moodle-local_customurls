<?php
require_once(dirname(dirname(__FILE__)).'../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('lib.php');
require_login(true);

//admin_externalpage_setup('customurls', '', null, '', array('pagelayout'=>'admin'));

$PAGE->set_context( context_SYSTEM::instance());
$PAGE->set_url($CFG->wwwroot.'/local/customurls/edit.php');
$PAGE->set_title("Edit custom urls");
$PAGE->set_heading("Edit custom urls");
$PAGE->set_pagelayout('admin');

global $CFG, $DB, $USER;
echo $OUTPUT->header();
echo "<h2>" . get_string('pluginname', 'local_customurls') ."</h2>";
echo '<link rel="stylesheet" type="text/css" href="styles.css">'; //Pick up the style sheet
echo '<script type="text/javascript"  src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js""></script>'; //Pick up javascript files
echo '<script type="text/javascript" src="/local/customurls/js/organictabs.jquery.js" ></script>'; //Pick up javascript files
echo "<script type='text/javascript'>
	 //<![CDATA[
	 $(function() {
	 $(\"#custom_urls_tab\").organicTabs();
			  });
			  //]]>
	  </script>";
$addednotes ='';
$addedurl ='';
$custom_name='';
$id ='';
$formi = 0;
$tablehead = "<tr><th>ID</th><th>User email</th><th>Notes</th><th>URL</th><th>Custom name</th><th>Edit</th></tr>";
////////////FORM HAS BEEN SUBMITTED///////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['submitted'])){
	//Pick up the vars
	$addednotes = $_POST['info'];
	$addedurl = $_POST['url'];
	$custom_name = $_POST['custom_name'];
	$userid = $_POST['user'];
	if (isset($_POST['id'])){
		$id = $_POST['id'];
	}
	if (isset($_POST['update'])){
		$update = $_POST['update'];
	}

	//////////NOTES VALIDATION///////////////////////////
	$addednotes_validate = preg_match('/^[A-Za-z0-9 ]/', $addednotes); //What validation to apply
	$addednotes_validate = (int)$addednotes_validate;
	if ($addednotes_validate == 0){
		if ($addednotes == ''){
			$addednotes_error = 'Notes: <span class="error">The notes field can not be blank</span><br />';
		}else{
			$addednotes_error = 'Notes: <span class="error">The notes can only contain letters or digits</span><br />';
		}
	}

	/////////URL VALIDATION///////////////////////////////

	if (url_exists($addedurl)){
		$addedurl_validate = 1;
	}else{
		$addedurl_validate = 0;
		if ($addedurl == ''){
			$addedurl_error = 'Url: <span class="error">The url field can not be blank</span><br />';
		}else{
			$addedurl_error = 'Url: <span class="error">The url is not valid</span><br />';
		}
	}

	////////CUSTOM NAME VALIDATION//////////////////////////
	$custom_name_validate = preg_match('~^[a-z\'/\'+-]+$~', $custom_name);
	$custom_name_validate = (int)$custom_name_validate;
	if ($custom_name_validate == 0 ){
		if ($custom_name == ''){
			$custom_name_error = 'Custom name: <span class="error">The custom name field can not be blank</span><br />';
		}else{
			$custom_name_error = 'Custom name: <span class="error">Your custom name contains invalid characters. It can only contain lower case letters and have no spaces.</span><br />';//ADD ERROR MESSAGE
		}
	}

	////////DUPLICATE CUSTOM NAME VALIDATION////////////////////////////////
	$sql = "SELECT * FROM {customurls} WHERE custom_name = ?";
	$duplicate_name_validate = $DB->get_records_sql($sql, array($custom_name));

	if(count($duplicate_name_validate) > 0){
		foreach($duplicate_name_validate as $k=>$v){
			$duplicatename_error  ='<b>Custom name: </b><span class="error">The custom name you have selected already exists.</span><br /> The associated url is<a href ="'. $v->url .'" target = "_blank" title = "'. $v->url .'"> '. $v->url .'</a><br />';
		}
	}

    /////WHAT ERROR MESSAGES DO WE NEED///////////////////////////
   // if ($custom_name_validate == 1 && $addednotes_validate == 1 && $addedurl_validate == 1 && count($duplicate_name_validate) == 0) {

	if(!isset ($_POST['id'])){

		if($custom_name_validate == 1 && $addednotes_validate == 1 && $addedurl_validate == 1 && count($duplicate_name_validate) == 0 ){
			$DB->insert_record('customurls', ['user' => $USER->id, 'info' => $addednotes, 'url' => $addedurl, 'custom_name' => $custom_name]);
			echo '<div class="notifysuccess">Changes saved</div>';
		} else{ //SUBMISSION FAILED
			echo 'Sorry your submitted entry was not completed: <br /><br />';
			if($addednotes_validate == 0){
				echo $addednotes_error;
			}
			if($addedurl_validate == 0){
				echo $addedurl_error;
			}
			if($custom_name_validate == 0){
				echo $custom_name_error;
			}
			if(count($duplicate_name_validate) > 0){
				echo $duplicatename_error;
			}

			echo '<br />';
		}
	}else{
		if($_POST['update'] == 1){
			$c_id = $DB->get_records_sql('SELECT id, custom_name FROM {customurls} WHERE custom_name = ?', array($custom_name));

			// foreach($c_id as $k=>$v){
				// if($v->id == $id || ($v->id == $id && $v->custom_name != $custom_name)){
					$c = new stdClass();
					$c->id = $id;
					$c->user = $USER->id;
					$c->userrole = '';
					$c->info = $addednotes;
					$c->url = $addedurl;
					$c->custom_name = $custom_name;
					$DB->update_record('customurls', $c);
					echo '<div class="notifysuccess">Changes saved</div>';
				// }else{
					// echo $duplicatename_error;
				// }
			//}
		}
	}

}else{
	$_POST['submitted']= '0';//NOTHING SUBMITTED
}
///////////END FORM HAS BEEN SUBMITTED///////////////////////////////////////////////////////////////////////////////////

echo '<div id="custom_urls_tab">
		<ul class="nav">
			<li class="nav-one"><a href="#search" class="current">Search / Add</a></li>';
			//echo '<li class="nav-two"><a href="#myentries">My Entries</a></li>';

			if(has_capability('local/customurls:admincustomurls', context_SYSTEM::instance())){
				//echo'<li class="nav-three"><a href="#libraryentries">Librarian Entries</a></li>';
				//echo '<li class="nav-four"><a href="#adminentries">Admin Entries</a></li>';
				echo '<li class="nav-five last"><a href="#allentries">All Entries</a></li>';
			}else{
				//echo' <li class="nav-three last"><a href="#libraryentries">Librarian Entries</a></li>';
			}
echo'</ul>

<div class="list-wrap">';

///////////////////////////////////////////SEARCH TAB///////////////////////////////////////////////////////
echo'<ul id="search" >
		<li><div class="search_urls"><form name="search" method="post" action="#">
		<input type="text" name="find"  value="Search Custom Urls..." onfocus="if(this.value == \'Search Custom Urls...\') { this.value = \'\';}" onblur="if(this.value == \'\') { this.value = \'Search Custom Urls...\';}" />
		<input type="hidden" name="searching" value="yes"/ >
		<select name="field">
			<option value="custom_name">Custom Name</option>
			<option value="url">Url</option>';
echo'</select>
	<input type="submit" name="search" value="Search" />
	</form></div>';
$result_customurls = '';

if(isset($_POST['searching'])){
	if ($_POST['field'] == 'url'){
		$tableheading = "<strong>Search by url results:</strong><br /><br />";
		$result_customurls = $DB->get_records_sql('SELECT c.*, u.email FROM {customurls} c JOIN {user} u ON c.user = u.id WHERE c.url = ? ORDER BY id DESC', array($_POST['find']));
	}
	if($_POST['field'] =='custom_name'){
		$tableheading = "<strong>Search by custom name results:</strong><br /><br />";
		$result_customurls = $DB->get_records_sql('SELECT c.*, u.email FROM {customurls} c JOIN {user} u ON c.user = u.id WHERE c.custom_name LIKE ?', array("%".$_POST['find']."%"));
	}

	if($result_customurls){
		echo $tableheading;
		echo "<table class ='thetable'>";
		echo $tablehead;
		foreach($result_customurls as $k=>$v){
			echo '<tr>';
			echo '<form name="update'. $v->id .''.$formi.'" method="post" action="" id ="customurl_form'. $v->id .''.$formi.'">';

			echo '<td>'. $v->id .'</td>';
			echo '<td>'. $v->email .'</td>';
			echo '<td class ="tdinfo"><textarea  rows="1" name="info"  id="info"onfocus="this.rows = \'3\'" onblur="this.rows=\'1\'" >'. $v->info .'</textarea></td>'; //Add the info field
			echo '<td><input name="url" type="text" id="url" value="'. $v->url .'"></td>'; //Add the url field
			echo '<td><input name="custom_name" type="text" id="custom_name" value="'. $v->custom_name .'"></td>'; //Add the custom name field
			echo '<input type="hidden" name="user" value="'. $v->user .'">';
			echo '<input type="hidden" name="id" value="'. $v->id .'">';
			echo '<input type="hidden" name="submitted" value="1">';
			echo '<input type="hidden" name="update" value="1">';
			echo '<td align="center">';
			if (is_siteadmin() or $USER->id == $v->user){
				echo "<a href='#' onclick='document.getElementById(\"customurl_form". $v->id .''.$formi ."\").submit(); return false;'>Update</a>";
				echo '<a href="delete.php?id='. $v->id .'">Delete</a>';
			}
			echo '</td></form></tr>';
		}
		echo '</table></li></ul>';
		$formi++;
	}else{
		echo "Nothing matches your query '<strong>". $_POST['find'] ."</strong>'<br /><br />";
		echo "<table class ='thetable'>";
		echo $tablehead;
		echo '<form name="addnewurl'.$formi.'" method="post" action="#" id ="customurl_form">'; //Start form to add new custom url
		echo "<tr><td>&nbsp;</td><td> &nbsp;</td>"; //Add empty cells
		echo '<td class ="tdinfo"><textarea  rows="1" name="info"  id="info" onfocus="this.rows = \'3\'" onblur="this.rows=\'1\'" ></textarea></td>'; //Add form field for custom url id
		echo '<td><input name="url" type="text" id="url"></td>';//Add form field for custom url
		echo '<td><input name="custom_name" type="text" id="custom_name"></td>'; //Add form field for custom url custom name
		echo '<input name="user" type="hidden" id="user" value="'.$USER->id.'">'; //Add hidden form field for user id
		echo '<input type="hidden" name="submitted" value="1">';
		echo '<td align="center"><a href="javascript: void(0);" onclick="document.addnewurl'.$formi.'.submit();return false;">Add</a></td>'; //Add link to add new custom url
		echo '</form>';//End form to add new custom url
		echo '</tr>';//End table row to add new custom url entry

		echo '</table></li></ul>';
		$formi++;
	}
}else{

	echo "<table class ='thetable'>";
	echo $tablehead;
	echo '<form name="addnewurl'.$formi.'" method="post" action="#" id ="customurl_form">'; //Start form to add new custom url
	echo "<tr><td>&nbsp;</td><td> &nbsp;</td>"; //Add empty cells
	echo '<td class ="tdinfo"><textarea  rows="1" name="info"  id="info" onfocus="this.rows = \'3\'" onblur="this.rows=\'1\'" ></textarea></td>'; //Add form field for custom url id
	echo '<td><input name="url" type="text" id="url"></td>';//Add form field for custom url
	echo '<td><input name="custom_name" type="text" id="custom_name"></td>'; //Add form field for custom url custom name
	echo '<input name="user" type="hidden" id="user" value="'.$USER->id.'">'; //Add hidden form field for user id
	echo '<input type="hidden" name="submitted" value="1">';
	echo '<td align="center"><a href="javascript: void(0);" onclick="document.addnewurl'.$formi.'.submit();return false;">Add</a></td>'; //Add link to add new custom url
	echo '</form>';//End form to add new custom url
	echo '</tr>';//End table row to add new custom url entry

	echo '</table></li></ul>';
	$formi++;
}

///////////////////////////////////////////ALL ENTRIES TAB///////////////////////////////////////////////////////
echo '<ul id="allentries" class="hide"><li>';

$result = $DB->get_records_sql('SELECT c.*, u.email FROM {customurls} c JOIN {user} u ON c.user = u.id ORDER BY id DESC', array());

echo "<table class ='thetable'><tr>";
echo $tablehead;

foreach($result as $k=>$v){
	echo '<tr>';
	echo '<form name="updateurl'. $v->id .''.$formi.'" method="post" action="" id ="customurl_form'. $v->id .''.$formi.'">';
	echo '<td>'. $v->id .'</td>';
	echo '<td>'. $v->email .'</td>';
	echo '<td class ="tdinfo"><textarea  rows="1" name="info"  id="info"onfocus="this.rows = \'3\'" onblur="this.rows=\'1\'" >'. $v->info .'</textarea></td>';
	echo '<td><input name="url" type="text" id="url" value="'. $v->url .'"></td>';
	echo '<td><input name="custom_name" type="text" id="custom_name" value="'. $v->custom_name .'"></td>';
	echo '<input type="hidden" name="user" value="'. $v->user .'">';
	echo '<input type="hidden" name="id" value="'. $v->id .'">';
	echo '<input type="hidden" name="submitted" value="1">';
	echo '<input type="hidden" name="update" value="1">';
	echo '<td align="center">';
	if (is_siteadmin() or $USER->id == $v->user){
		echo'<a href="javascript: void(0);" onclick="document.updateurl'. $v->id .''.$formi.'.submit();return false;">Update</a>';
		echo' <a href="delete.php?id='. $v->id .'">Delete</a>';
	}
	echo '</td>';
	echo' </form>';//End form to upadte custom url
	echo'</tr>';
}

$formi++;

echo "</table></li></ul>";
echo "</div> <!-- END List Wrap -->
 </div> <!-- END Organic Tabs (Example One) -->";
echo $OUTPUT->footer();
?>
