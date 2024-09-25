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
 * Class containing helper functions for customurls
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls;

use moodle_url;
use stdClass;
/**
 * Api class
 */
class api {
    /**
     * Check if the URL exist.
     * @param string $url (string) URL to check.
     * @return bool Returns TRUE if the URL exists; FALSE otherwise.
     */
    public static function url_exists($url) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $url = trim($url);
        if (empty($url)) {
            return false;
        }

        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_URL' => $url,
            'CURLOPT_CONNECTTIMEOUT' => 10,
        ]);
        $curl->head($url);
        $info = $curl->get_info();
        return ($info['http_code'] == 200);
    }

    /**
     * Takes the path portion of a url and searches the database for the record.
     * Increments count, if found.
     *
     * @param string $uri Path
     * @return stdClass | bool The record for the customurl, or false.
     */
    public static function get_customurl($uri) {
        global $DB;
        $uri = trim($uri, "/");
        // Unless we prohibit ? in the custom_name field, it could be possible there is more
        // than one ? added to the url. So pick the last one. But this would mismatch where there
        // is just one legitimate ?. So we need to ban it altogether.
        // Perhaps add a check of some sort in the upgrade.php file. But what to do with now illegal entries?
        $lastq = strrpos($uri, '?');
        if ($lastq !== false) {
            $uri = substr($uri, 0, $lastq);
        }
        $record = $DB->get_record('local_customurls', ['custom_name' => $uri]);
        if (!$record) {
            return false;
        }
        $record->lastaccessed = time();
        $record->accesscount++;
        $DB->update_record('local_customurls', $record);
        return $record;
    }

    /**
     * Given a path return a url
     *
     * @param string $name
     * @return moodle_url
     */
    public static function get_customname_as_url($name): moodle_url {
        if (strpos($name, '/') !== 0) {
            $name = '/' . $name;
        }
        return new moodle_url($name);
    }

    /**
     * Creates a persistent record from a stdClass
     *
     * @param stdClass $record
     * @return customurl
     */
    public static function create_customurl($record) {
        $customurl = new customurl(0, $record);
        $customurl->create();
        return $customurl;
    }
}
