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

$PAGE->set_context($context);
$PAGE->set_heading(get_string('pluginname', 'local_customurls'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_customurls'));
$PAGE->set_url($CFG->wwwroot.'/local/customurls/index.php');

echo $OUTPUT->header();

$table = new \local_customurls\tables\customurls_table('customurls');
$table->no_sorting('actions');
if ($canedit) {
    $new = new action_link(new moodle_url('/local/customurls/manage.php', ['action' => 'new']),
    get_string('newcustomurl', 'local_customurls'), null,
    ['class' => 'btn btn-primary'],
    new pix_icon('e/insert_edit_link', get_string('newcustomurl', 'local_customurls')));
    echo $OUTPUT->render($new);
}

$table->set_sql('*', "{customurls}", '1=1');

$table->define_baseurl(new moodle_url("/local/customurls/index.php"));
$table->out(10, false);

echo $OUTPUT->footer();