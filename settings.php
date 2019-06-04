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
 * This file defines the admin settings for this plugin
 *
 * @package   local_customurls
 * @copyright 2019 Solent University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
$settings = new admin_settingpage('local_customurls', new lang_string('pluginname', 'local_customurls'));

$link = '<a href="' . $CFG->wwwroot.'/local/customurls/edit.php">' . get_string('pluginname', 'local_customurls') . '</a>';
$settings->add(new admin_setting_heading('local_customurls_link', '', $link));

$ADMIN->add('localplugins', $settings);

// $ADMIN->add('root', new admin_category('tweaks', 'Custom urls'));
// $ADMIN->add('tweaks', new admin_externalpage('customurls', 'Manage urls',
// $CFG->wwwroot.'/local/customurls/edit.php','local/customurls:managecustomurls'));
