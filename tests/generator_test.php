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
 * Test customurls generator functions
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_customurls_generator_testcase extends advanced_testcase {
    public function test_create() {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        set_config('checkurl', 0, 'local_customurls');
        $count = $DB->count_records('customurls');
        $customurl = $generator->create_customurl([
            'custom_name' => 'cu1',
            'url' => '/my',
            'info' => 'cu1 description'
        ]);
        $count++;
        $this->assertEquals($count, $DB->count_records('customurls'));
        $this->assertSame('cu1', $customurl->get('custom_name'));
    }

    public function test_setup_courses_and_customurls() {
        global $DB;
        $this->resetAfterTest();
        set_config('checkurl', 0, 'local_customurls');
        $num = 2;
        $generator = $this->getDataGenerator()->get_plugin_generator('local_customurls');

        $countcourses = $DB->count_records('course', ['category' => 1]);
        $countcustomurls = $DB->count_records('customurls');

        $candc = $generator->setup_courses_and_customurls($num);

        $countcourses = $countcourses + $num;
        $countcustomurls = $countcustomurls + $num;

        $this->assertEquals($countcourses, $DB->count_records('course', ['category' => 1]));
        $this->assertEquals($countcustomurls, $DB->count_records('customurls'));
        $this->assertEquals($num, count($candc));
        for ($i = 0; $i < $num; $i++) {
            $customurl = $candc[$i]['customurl'];
            $course = $candc[$i]['course'];

            $coursepath = '/course/view.php?id=' . $course->id;
            $url = $customurl->get('url');
            $this->assertEquals($coursepath, $url);

            $shortname = 'CU' . $i;
            $alias = $customurl->get('custom_name');
            $this->assertEquals($alias, $shortname);
        }
    }
}
