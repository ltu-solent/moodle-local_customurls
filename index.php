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
 * Main landing page for customurls
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_customurls\customurl;

require_once('../../config.php');

require_login(null, false);

if (isguestuser()) {
    throw new moodle_exception('cannotviewreport', 'error');
}

$context = context_system::instance();
$canedit = false;
if (has_capability('local/customurls:managecustomurls', $context)) {
    $canedit = true;
}
$action = optional_param('action', '', PARAM_ALPHA);
$download = optional_param('download', '', PARAM_ALPHA);

if ($canedit) {
    $resetallconfirm = optional_param('resetallconfirm', 0, PARAM_BOOL);
    if ($resetallconfirm && confirm_sesskey()) {
        customurl::reset_count();
        redirect(new moodle_url('/local/customurls/index.php'),
            get_string('allcountershavebeenreset', 'local_customurls'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    $table = new \local_customurls\tables\customurls_table('customurls', $download);
    if ($table->is_downloading()) {
        $table->download();
    }
}

$PAGE->set_context($context);
$PAGE->set_heading(get_string('pluginname', 'local_customurls'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_customurls'));
$PAGE->set_url($CFG->wwwroot.'/local/customurls/index.php');

echo $OUTPUT->header();

if ($action == 'resetall' && $canedit) {
    $heading = new lang_string('confirmreset', 'local_customurls');
    echo html_writer::tag('h3', $heading);
    $reseturl = new moodle_url('/local/customurls/index.php', [
        'resetallconfirm' => true,
        'sesskey' => sesskey(),
    ]);
    $resetbutton = new single_button($reseturl, $heading);
    echo $OUTPUT->confirm(
        new lang_string('confirmresetdesc', 'local_customurls'),
        $resetbutton,
        new moodle_url('/local/customurls/index.php')
    );
    echo $OUTPUT->footer();
    die();
}

$table = new \local_customurls\tables\customurls_table('customurls');
$table->no_sorting('actions');
if ($canedit) {
    $new = new action_link(new moodle_url('/local/customurls/manage.php', ['action' => 'new']),
        get_string('newcustomurl', 'local_customurls'),
        null,
        ['class' => 'btn btn-primary'],
        new pix_icon('e/insert_edit_link', get_string('newcustomurl', 'local_customurls'))
    );
    echo $OUTPUT->render($new);

    $resetall = new action_link(
        new moodle_url('/local/customurls/index.php', [
            'action' => 'resetall',
            'sesskey' => sesskey(),
        ]),
        get_string('resetcount', 'local_customurls'),
        null,
        ['class' => 'btn btn-warning pull-right'],
        new pix_icon('t/reset', get_string('resetcount', 'local_customurls'))
    );
    echo $OUTPUT->render($resetall);
}

$searchform = new \local_customurls\forms\search_form();
if ($formdata = $searchform->get_data()) {
    // Only display unbroken links? We might sometimes get false negatives.
    // Search also for space replacing with "-" or ""?
    $where = 'custom_name LIKE :query';
    $table->set_sql('*', "{customurls}", $where, ['query' => '%' . $formdata->query . '%']);
}

echo $searchform->display();

$table->out(100, false);

echo $OUTPUT->footer();
