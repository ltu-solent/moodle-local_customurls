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
use moodle_url;
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
                'type' => PARAM_TEXT,
                'default' => '/'
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

    /**
     * A valid url, could be a full qualified url, or a localurl. The default path allows anything,
     * so this function will check for anything that shouldn't be there.
     *
     * @param string $url
     * @return bool | lang_string
     */
    protected function validate_url($url) {

        $islocalurl = strpos($url, '/') === 0;
        if ($islocalurl) {
            $murl = new moodle_url($url);
            $url = $murl->out();
        }
        $cleanurl = clean_param($url, PARAM_URL);
        if ($cleanurl !== $url) {
            return new lang_string('unclean', 'local_customurls');
        }
        $whitelist = trim(get_config('local_customurls', 'whitelistdomainpattern'));
        if (!empty($whitelist)) {
            $targetdomains = explode(',', $whitelist);
            if (count($targetdomains) > 0) {
                $ok = false;
                foreach ($targetdomains as $targetdomain) {
                    if (strpos($url, $targetdomain) !== false) {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    return new lang_string('invaliddomain', 'local_customurls', $targetdomain);
                }
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
            return new lang_string('urlnotexists', 'local_customurls');
        }
        return true;
    }
}