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
 * Test persistent customurl object and processes.
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls;

use advanced_testcase;
use Exception;
use local_customurls_generator;

/**
 * Customurl test
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_customurls\customurl
 * @group sol
 */
final class customurl_test extends advanced_testcase {

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        // Check url is off as it's a little unreliable to do remote tests. The url is still validated for being well-formed.
        // And checked against a whitelist and blocked urls.
        set_config('checkurl', 0, 'local_customurls');
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
    }

    /**
     * Test setting customurl with data from provider
     * @param array $data Data required to create customurl
     * @param bool $status Expected result
     * @dataProvider set_customurl_provider
     *
     * @return void
     */
    public function test_set_customurl($data, $status): void {
        /** @var local_customurls_generator $gen */
        $gen = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        if ($status !== true) {
            $this->expectException($status);
        }
        $customurl = $gen->create_customurl($data);

        if ($status == true) {
            $this->assertNotFalse($customurl);
            $this->assertSame($data['custom_name'], $customurl->get('custom_name'));
            $this->assertSame($data['url'], $customurl->get('url'));
        } else {
            $this->assertFalse($customurl);
        }
    }

    /**
     * Provider for test_set_customurl
     *
     * @return array
     */
    public static function set_customurl_provider(): array {
        return [
            'plainpath' => [
                [
                    'custom_name' => 'plainpath',
                    'url' => '/my',
                ],
                true,
            ],
            'hyphenpath' => [
                [
                    'custom_name' => 'hyphen-path',
                    'url' => '/my',
                ],
                true,
            ],
            'slashpath' => [
                [
                    'custom_name' => 'slash/path',
                    'url' => '/my',
                ],
                true,
            ],
            'slash-hyphen-path' => [
                [
                    'custom_name' => 'slash/hythen-path',
                    'url' => '/my',
                ],
                true,
            ],
            'urlparam' => [
                [
                    'custom_name' => 'path?one=true',
                    'url' => '/my',
                ],
                \core\invalid_persistent_exception::class, // Do I want this to pass?
            ],
            'urlparams' => [
                [
                    'custom_name' => 'path?one=true&two=some-stuff',
                    'url' => '/my',
                ],
                \core\invalid_persistent_exception::class,
            ],
            'spaces' => [
                [
                    'custom_name' => 'space one two',
                    'url' => '/my',
                ],
                \core\invalid_persistent_exception::class, // Do I want this to pass?
            ],
            'percent' => [
                [
                    'custom_name' => 'space%20one%20two',
                    'url' => '/my',
                ],
                \core\invalid_persistent_exception::class,
            ],
        ];
    }

    /**
     * Validates provided urls
     * @dataProvider validate_url_provider
     *
     * @param string $url
     * @return void
     */
    public function test_validate_url($url): void {
        global $CFG;
        /** @var local_customurls_generator $gen */
        $gen = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        // Any url is fine, local or remote, except youtube.
        set_config('whitelistdomainpattern', '', 'local_customurls');
        set_config('curlsecurityblockedhosts', 'www.youtube.com');
        try {
            $customurl = $gen->create_customurl(['url' => $url]);
            $this->assertNotFalse($customurl);
            $this->assertSame($url, $customurl->get('url'));
        } catch (Exception $ex) {
            $this->assertStringContainsStringIgnoringCase(
                get_string('blockedurl', 'local_customurls', $url),
                $ex->getMessage()
            );
        }
        $CFG->wwwroot = 'http://example.com/phpunit';
        $domain = $CFG->wwwroot . "\n";
        set_config('whitelistdomainpattern', $domain, 'local_customurls');
        set_config('curlsecurityblockedhosts', '');

        try {
            // Only local domain is ok.
            $customurl = $gen->create_customurl(['url' => $url]);
            $this->assertNotFalse($customurl);
            $this->assertSame($url, $customurl->get('url'));
        } catch (Exception $ex) {
            $this->assertStringContainsStringIgnoringCase(
                get_string('invaliddomain', 'local_customurls', ['url' => $url, 'domains' => $CFG->wwwroot]),
                $ex->getMessage()
            );
        }

    }

    /**
     * Provider for validate_url
     *
     * @return array
     */
    public static function validate_url_provider(): array {
        return [
            'google' => ['https://www.google.com'],
            'wikipedia' => ['https://en.wikipedia.org/wiki/GNOME'],
            'dashboard' => ['/my'],
            'coursesearch' => ['/course/index.php'],
            'youtube' => ['https://www.youtube.com/watch?v=9bZkp7q19f0'],
            'solent' => ['https://solent.ac.uk'],
            'learn' => ['https://learn.solent.ac.uk'],
        ];
    }

    /**
     * Test the duplicate customnames can't be created.
     *
     * @return void
     */
    public function test_report_duplicate_url(): void {
        $this->expectException(\core\invalid_persistent_exception::class);
        $this->expectExceptionMessage('custom_name: Custom path already exists');
        set_config('whitelistdomainpattern', '', 'local_customurls');
        /** @var local_customurls_generator $gen */
        $gen = $this->getDataGenerator()->get_plugin_generator('local_customurls');
        $url = 'https://www.google.com';
        $path = 'mypath';
        $gen->create_customurl(['url' => $url, 'custom_name' => $path]);
        // Try to create the same customurl.
        $gen->create_customurl(['url' => $url, 'custom_name' => $path]);
    }
}
