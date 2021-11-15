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
 * Check urls task
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls\task;

use local_customurls\api;

defined('MOODLE_INTERNAL') || die();

class checkurls extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('checkurls', 'local_customurls');
    }

    public function execute() {
        global $DB;
        // Get them all. Might need to increase timeout?
        $customurls = $DB->get_records('customurls');
        foreach ($customurls as $customurl) {
            $urlexists = api::url_exists($customurl->url);
            if (!$urlexists && $customurl->isbroken) {
                continue;
            }
            if ($urlexists && !$customurl->isbroken) {
                continue;
            }
            $oldstatus = $customurl->isbroken;
            $customurl->isbroken = $urlexists;
            $customurl->oldstatus = $oldstatus;
            $statusstring = ($urlexists) ? 'urlunbroken' : 'urlbroken';
            mtrace(get_string($statusstring, 'local_customurls'. $customurl));
            $DB->update_record('customurls', $customurl);
        }

        return true;
    }
}
