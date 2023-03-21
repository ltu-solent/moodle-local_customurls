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
 * Generator class for customurls
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_customurls\api;

class local_customurls_generator extends component_generator_base
{

    /** @var int Number of created customurls. */
    protected $customurlscount = 0;

    /**
     * Reset process.
     *
     * Do not call directly.
     *
     * @return void
     */
    public function reset() {
        $this->customurlscount = 0;
    }

    public function create_customurl($record = null) {
        $this->customurlscount++;
        $i = $this->customurlscount;
        $record = (object)$record;

        if (!isset($record->custom_name)) {
            $record->custom_name = "/customurl$i";
        }

        if (!isset($record->info)) {
            $record->info = "{$record->custom_name} description";
        }

        $customurl = api::create_customurl($record);

        return $customurl;
    }

    /**
     * Generates a specified number of courses with matching customurls
     *
     * @param integer $count
     * @return array An array of ['customurl' => {}, 'course' => {}]
     */
    public function setup_courses_and_customurls($count = 1) : array {
        global $USER;
        $generator = $this->datagenerator;
        $coursesandcustomurls = [];
        for ($i = 0; $i < $count; $i++) {
            $course = $generator->create_course([
                'shortname' => "CU{$i}"
            ]);
            $record = new stdClass();
            $record->custom_name = $course->shortname;
            $record->user = $USER->id;
            $record->url = '/course/view.php?id=' . $course->id;
            $record->info = '{$course->shortname} description';
            $customurl = $this->create_customurl($record);
            $coursesandcustomurls[] = [
                'customurl' => $customurl,
                'course' => $course
            ];
        }
        return $coursesandcustomurls;
    }
}
