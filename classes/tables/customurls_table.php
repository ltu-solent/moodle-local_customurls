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

use local_customurls\api;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/tablelib.php");
use context_system;
use core_user;
use html_writer;
use moodle_url;
use pix_icon;
use single_button;
use table_sql;

/**
 * Customurl table listing all filtered customurls
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customurls_table extends table_sql {

    /**
     * {@inheritDoc}
     *
     * @param string $uniqueid
     * @param string $downloadformat Used to determine if a download is happening.
     */
    public function __construct($uniqueid, $downloadformat = null) {
        parent::__construct($uniqueid);
        $context = context_system::instance();
        $canmanage = has_capability('local/customurls:managecustomurls', $context);
        $this->set_attribute('id', 'local_customurls-customurls');
        $this->useridfield = 'user';
        $this->define_baseurl(new moodle_url("/local/customurls/index.php"));
        $where = '1=1';
        $this->set_sql('*', "{customurls}", $where);
        $columns = [];
        $columnheadings = [];
        if ($canmanage) {
            $columns = [
                'id',
                'user',
                'info',
                'custom_name',
                'url',
                'lastaccessed',
                'isbroken',
                'accesscount',
            ];
            $columnheadings = [
                get_string('id', 'local_customurls'),
                get_string('createdby', 'local_customurls'),
                get_string('description', 'local_customurls'),
                get_string('customlink', 'local_customurls'),
                get_string('redirectto', 'local_customurls'),
                get_string('lastaccessed', 'local_customurls'),
                get_string('urlstatus', 'local_customurls'),
                get_string('accesscount', 'local_customurls'),
            ];
            $this->downloadable = true;
            $this->is_downloading($downloadformat, 'customurls', 'customurls');
            // Only output actions if we're not downloading.
            if (!$this->is_downloading()) {
                $columns[] = 'actions';
                $columnheadings[] = get_string('actions', 'local_customurls');
            }
            $this->show_download_buttons_at([TABLE_P_BOTTOM]);
        } else {
            $columns = [
                'id',
                'info',
                'custom_name',
                'url',
            ];
            $columnheadings = [
                get_string('id', 'local_customurls'),
                get_string('description', 'local_customurls'),
                get_string('customlink', 'local_customurls'),
                get_string('redirectto', 'local_customurls'),
            ];
        }
        $this->define_columns($columns);
        $this->define_headers($columnheadings);
        $this->no_sorting('actions');
        $this->no_sorting('info');
        $this->sortable(true, 'id', SORT_DESC);
        $this->collapsible(false);
    }

    /**
     * Actions column
     *
     * @param stdClass $col Data for current row
     * @return string Content for cell
     */
    public function col_actions($col) {
        global $OUTPUT;
        $actions = [];
        if (!has_capability('local/customurls:managecustomurls', context_system::instance())) {
            return '';
        }
        if ($this->is_downloading()) {
            return '';
        }

        $params = ['action' => 'edit', 'id' => $col->id];
        $edit = new moodle_url('/local/customurls/manage.php', $params);
        $actions[] = html_writer::link($edit,
            $OUTPUT->pix_icon('i/edit', get_string('edit')));

        if ($col->accesscount > 0) {
            $reset = new moodle_url('/local/customurls/manage.php', [
                'id' => $col->id,
                'sesskey' => sesskey(),
                'action' => 'resetcount',
            ]);
            $actions[] = html_writer::link($reset,
                $OUTPUT->pix_icon('t/reset', get_string('resetcount', 'local_customurls')));
        }

        $params['action'] = 'delete';
        $delete = new moodle_url('/local/customurls/manage.php', $params);
        $actions[] = html_writer::link($delete,
            $OUTPUT->pix_icon('i/delete', get_string('delete')));

        return implode(" | ", $actions);
    }

    /**
     * IsBroken column
     *
     * @param stdClass $col Data for current row
     * @return string Content for cell
     */
    public function col_isbroken($col) {
        global $OUTPUT;
        $status = 'statusok';
        $icon = 'unflagged';
        if ($col->isbroken) {
            $status = 'statusbroken';
            $icon = 'flagged';
        }
        $pix = new pix_icon('i/' . $icon, new \lang_string($status, 'local_customurls'));
        return $OUTPUT->render($pix);
    }

    /**
     * Returns a linked custom_name
     *
     * @param stdClass $col
     * @return string link html
     */
    public function col_custom_name($col) {
        $customurl = api::get_customname_as_url($col->custom_name);
        return html_writer::link($customurl, $col->custom_name);
    }

    /**
     * Url column
     *
     * @param stdClass $col Data for current row
     * @return string Content for cell
     */
    public function col_url($col) {
        $truncatedurl = \core_text::substr($col->url, 0, 100);
        if ($truncatedurl != $col->url) {
            $truncatedurl .= '...';
        }
        return html_writer::link($col->url, $truncatedurl);
    }

    /**
     * User column
     *
     * @param stdClass $col Data for current row
     * @return string Content for cell
     */
    public function col_user($col) {
        // Check for deleted user.
        $createdby = core_user::get_user($col->user);
        if (!$createdby || $createdby->deleted) {
            return get_string('deleteduser', 'local_customurls');
        }
        return fullname($createdby);
    }

    /**
     * Last accessed column
     *
     * @param stdClass $col Data for current row
     * @return string Content for cell
     */
    public function col_lastaccessed($col) {
        if ($col->lastaccessed == 0) {
            return "-";
        }
        return userdate($col->lastaccessed, get_string('strftimedatetimeshort', 'core_langconfig'));
    }

    /**
     * Download
     *
     * @return void
     */
    public function download() {
        unset($this->columns['actions']);
        \core\session\manager::write_close();
        $this->out(0, false);
        exit;
    }
}
