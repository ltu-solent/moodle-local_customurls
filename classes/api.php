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

use curl;

defined('MOODLE_INTERNAL') || die();

/**
 * Api class
 */
class api {
    /**
     * Check if the URL exist.
     * @param url (string) URL to check.
     * @return Returns TRUE if the URL exists; FALSE otherwise.
     * @public static
     */
    public static function url_exists($url) {
        global $CFG;
        $url = trim($url);
        if (empty($url)) {
            return false;
        }
        $debugging = isset($CFG->debug);
        if ($debugging) {
            return true;
        }
        $curl = new curl(['proxy' => true, 'debug' => $debugging]);
        $curl->setopt(['CURLOPT_URL' => $url]);
        $response = $curl->head($url);
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
        $record = $DB->get_record('customurls', ['custom_name' => $uri]);
        if (!$record) {
            return false;
        }
        $record->lastaccessed = time();
        $record->accesscount++;
        $DB->update_record('customurls', $record);
        return $record;
    }
}