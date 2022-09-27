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
 * API unit tests
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_customurls;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
/**
 * API test
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local\customurls\api
 */
class api_test extends advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
        set_config('checkurl', 0, 'local_customurls');
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
    }

    /**
     * Get customurl function
     * @param string $customname Name of path required
     * @param bool $status Expected outcome
     * @dataProvider get_customurl_provider
     *
     * @return void
     */
    public function test_get_customurl($customname, $status) {
        $gen = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        $customurl = $gen->create_customurl(['custom_name' => $customname, 'url' => '/my']);
        $this->assertNotFalse($customurl);
        $this->assertSame($customname, $customurl->get('custom_name'));
        $geturl = \local_customurls\api::get_customurl($customname);
        $this->assertNotFalse($geturl);
        $this->assertSame($geturl->custom_name, $customname);

        // Should also be resilient to extra params being added by the user.
        $added = '?utm_source=Southampton%20Solent%20University&utm_medium=email' .
            '&utm_campaign=12345678_JI%202022%2019%20August&dm_i=MMMM%2CMMMMMM%2CMMMMMM%2CMMMMM%2C1';
        $geturl = \local_customurls\api::get_customurl($customname . $added);
        $this->assertNotFalse($geturl);
        $this->assertSame($geturl->custom_name, $customname);
    }

    /**
     * Provider for get_customurl
     *
     * @return array
     */
    public function get_customurl_provider(): array {
        return [
            'plainpath' => [
                'plainpath',
                true
            ],
            'hyphenpath' => [
                'hyphen-path',
                true
            ],
            'slashpath' => [
                'slash/path',
                true
            ],
            'slash-hyphen-path' => [
                'slash/hythen-path',
                true
            ]
            // The following can't now be saved, so can't be tested.
            // 'urlparam' => [
            //     'path?one=true',
            //     true // Do I want this to pass?
            // ],
            // 'spaces' => [
            //     'space one two',
            //     true // Do I want this to pass?
            // ],
            // 'percent' => [
            //     'space%20one%20two',
            //     true
            // ]
        ];
    }
}