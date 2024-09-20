<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Customurls manage links page
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_customurls\customurl;
use local_customurls\forms\customurl_form;

require_login(null, false);

$id = optional_param('id', 0, PARAM_INT); // CustomurlID - 0 new assumed.
$action = optional_param('action', 'new', PARAM_ALPHA);
$confirmdelete = optional_param('confirmdelete', null, PARAM_BOOL);

$context = context_system::instance();
require_capability('local/customurls:managecustomurls', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

if (!in_array($action, ['edit', 'delete', 'new', 'resetcount'])) {
    $action = 'new';
}
$pageparams = [
    'action' => $action,
    'id' => $id,
];

$customurl = null;
$form = null;

if ($action == 'edit' || $action == 'delete' || $action == 'resetcount') {
    if ($id == 0) {
        throw new moodle_exception('invalidid', 'local_customurls');
    }
} else {
    $action = 'new';
}

$customurl = new customurl($id);
$customdata = [
    'persistent' => $customurl,
    'user' => $USER->id,
];

if ($confirmdelete && confirm_sesskey()) {
    $customname = $customurl->get('custom_name');
    $customurl->delete();
    redirect(new moodle_url('/local/customurls/index.php'),
        get_string('deleted', 'local_customurls', $customname),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

if ($action == 'resetcount' && confirm_sesskey()) {
    $customname = $customurl->get('custom_name');
    $customurl->set('accesscount', 0);
    $customurl->save();
    redirect(new moodle_url('/local/customurls/index.php'),
        get_string('countreset', 'local_customurls', $customname),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$PAGE->set_url($CFG->wwwroot.'/local/customurls/manage.php', $pageparams);
$form = new customurl_form($PAGE->url->out(false), $customdata);
if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/customurls/index.php'));
}
if ($formdata = $form->get_data()) {
    if (empty($formdata->id)) {
        $customurl = new customurl(0, $formdata);
        $customurl->create();
        // We are done, so let's redirect somewhere.
        redirect(new moodle_url('/local/customurls/index.php'),
            get_string('newsaved', 'local_customurls'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $customurl = new customurl($formdata->id);
        if ($action == 'edit') {
            $customurl->from_record($formdata);
            $customurl->update();
            redirect(new moodle_url('/local/customurls/index.php'),
                get_string('updated', 'local_customurls', $formdata->custom_name),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }
    }
}


$PAGE->set_title(get_string('managecustomurls', 'local_customurls'));
$PAGE->set_heading(get_string('managecustomurls', 'local_customurls'));

echo $OUTPUT->header();

if ($action == 'delete') {
    $heading = new lang_string('confirmdelete', 'local_customurls', $customurl->get('custom_name'));
    echo html_writer::tag('h3', $heading);
    $deleteurl = new moodle_url('/local/customurls/manage.php', [
        'action' => 'delete',
        'confirmdelete' => true,
        'id' => $id,
        'sesskey' => sesskey(),
    ]);
    $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');
    echo $OUTPUT->confirm(
        get_string('confirmdelete', 'local_customurls', $customurl->get('custom_name')),
        $deletebutton, new moodle_url('/local/customurls/index.php', ['id' => $id]));
} else {
    $heading = new lang_string('newcustomurl', 'local_customurls');
    if ($id > 0) {
        $heading = new lang_string('editcustomurl', 'local_customurls');
    }
    echo html_writer::tag('h3', $heading);
    $extrahelp = get_config('local_customurls', 'customurlshelp');
    if ($extrahelp) {
        echo $extrahelp;
    }

    $form->display();
}

echo $OUTPUT->footer();
