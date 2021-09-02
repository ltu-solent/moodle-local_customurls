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
 * 404 page
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$customurl = \local_customurls\api::get_customurl($_SERVER['REQUEST_URI']);

if ($customurl) {
    header('Location:' . $customurl->url);
    exit();
}

$PAGE->set_context ( context_system::instance () );
$PAGE->set_pagelayout ('base');
$PAGE->set_title (get_string('pagenotfound', 'local_customurls'));
$PAGE->set_heading (get_string('pagenotfound', 'local_customurls'));
$PAGE->set_url (new moodle_url('/404.php'));

echo $OUTPUT->header();
$content = new \local_customurls\output\fourohfour();
echo $OUTPUT->render($content);
echo $OUTPUT->footer();