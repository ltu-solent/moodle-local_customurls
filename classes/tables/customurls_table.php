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
 * Table to display custom urls
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls\tables;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/tablelib.php");
use context_system;
use core_user;
use html_writer;
use moodle_url;
use table_sql;

class customurls_table extends table_sql {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $context = context_system::instance();
        $canmanage = has_capability('local/customurls:managecustomurls', $context);
        $this->useridfield = 'user';
        if ($canmanage) {
            $columns = [
                'user',
                'info',
                'custom_name',
                'url',
                'lastaccessed',
                'accesscount',
                'actions'
            ];
            $columnheadings = [
                get_string('createdby', 'local_customurls'),
                get_string('description', 'local_customurls'),
                get_string('customlink', 'local_customurls'),
                get_string('redirectto', 'local_customurls'),
                get_string('lastaccessed', 'local_customurls'),
                get_string('accesscount', 'local_customurls'),
                get_string('actions', 'local_customurls')
            ];
        } else {
            $columns = [
                'info',
                'custom_name',
                'url'
            ];
            $columnheadings = [
                get_string('description', 'local_customurls'),
                get_string('customlink', 'local_customurls'),
                get_string('redirectto', 'local_customurls')
            ];
        }
        $this->define_columns($columns);
        $this->define_headers($columnheadings);
        $this->no_sorting('actions');
        $this->no_sorting('info');
        $this->collapsible(false);
    }

    public function col_actions($col) {
        if (has_capability('local/customurls:managecustomurls', context_system::instance())) {
            $params = ['action' => 'edit', 'id' => $col->id];
            $edit = new moodle_url('/local/customurls/manage.php', $params);
            $html = html_writer::link($edit, get_string('edit'));

            $params['action'] = 'delete';
            $delete = new moodle_url('/local/customurls/manage.php', $params);
            $html .= " " . html_writer::link($delete, get_string('delete'));
            return $html;
        }
    }

    public function col_user($col) {
        // Check for deleted user.
        $createdby = core_user::get_user($col->user);
        if ($createdby->deleted) {
            return get_string('deleteduser', 'local_customurls');
        }
        return fullname($createdby);
    }

    public function col_lastaccessed($col) {
        if ($col->lastaccessed == 0) {
            return "-";
        }
        return userdate($col->lastaccessed, get_string('strftimedatetimeshort', 'core_langconfig'));
    }
}