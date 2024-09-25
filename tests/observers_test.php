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

namespace local_customurls;

use advanced_testcase;

/**
 * Observers test
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_customurls\observers
 * @group sol
 */
class observers_test extends advanced_testcase {

    public function test_delete_user() {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $adminuserid = get_admin()->id;
        set_config('checkurl', 0, 'local_customurls');

        $cugenerator = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        // No need to check the urls are valid.

        $urlscourses = $cugenerator->setup_courses_and_customurls(2);
        $count = $DB->count_records('local_customurls', ['usermodified' => $user->id]);
        $this->assertEquals(2, $count);
        // Ownership is going to transfer to siteadmin - check they don't have any bookmarks.
        $count = $DB->count_records('local_customurls', ['usermodified' => $adminuserid]);
        $this->assertEquals(0, $count);

        delete_user($user);
        $count = $DB->count_records('local_customurls', ['usermodified' => $user->id]);
        $this->assertEquals(0, $count);

        // Ownership transfers to the main siteadmin, rather than deleting the urls.
        $count = $DB->count_records('local_customurls', ['usermodified' => $adminuserid]);
        $this->assertEquals(2, $count);
    }
}

