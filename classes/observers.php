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
 * Event observers
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls;

/**
 * Event observers class
 */
class observers {

    /**
     * Move customurls associated with this user to siteadmin.
     *
     * @param \core\event\user_deleted $event
     * @return void
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;
        $adminuserid = get_admin()->id;
        $DB->set_field('customurls', 'user',
                    $adminuserid, ['user' => $event->objectid]);
    }
}
