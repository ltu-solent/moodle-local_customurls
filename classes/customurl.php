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
 * Customurl instance
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls;

use core\persistent;
use lang_string;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class customurl extends persistent {
    /**
     * Table name for customurls.
     */
    const TABLE = 'customurls';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        global $CFG, $USER;
        return [
            'user' => [
                'type' => PARAM_INT,
                'default' => $USER->id
            ],
            'info' => [
                'type' => PARAM_TEXT
            ],
            'url' => [
                'type' => PARAM_URL,
                'default' => $CFG->wwwroot
            ],
            'custom_name' => [
                'type' => PARAM_PATH
            ],
            'lastaccessed' => [
                'type' => PARAM_INT,
                'default' => 0
            ],
            'accesscount' => [
                'type' => PARAM_INT,
                'default' => 0
            ]
        ];
    }

    protected function validate_url($url) {
        $targetdomain = get_config('local_customurls', 'targetdomainpattern');
        if (!empty($targetdomain)) {
            if (strpos($url, $targetdomain) === false) {
                return new lang_string('invaliddomain', 'local_customurls', $targetdomain);
            }
        }
        // Is it a valid url, though? PARAM_URL should check this.
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return new lang_string('invalidurl', 'local_customurls');
        }

        $curlhelper = new \core\files\curl_security_helper;
        if ($curlhelper->url_is_blocked($url)) {
            return new lang_string('blockedurl', 'local_customurls', $url);
        }
        if (!helper::url_exists($url)) {
            return new lang_string('invalidurl', 'local_customurls');
        }
        return true;
    }
}