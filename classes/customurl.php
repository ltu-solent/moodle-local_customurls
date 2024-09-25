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

use context_system;
use core\persistent;
use lang_string;
use moodle_url;

/**
 * Customurl persistent class
 */
class customurl extends persistent {
    /**
     * Table name for customurls.
     */
    const TABLE = 'local_customurls';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        global $USER;
        return [
            'info' => [
                'type' => PARAM_TEXT,
            ],
            'url' => [
                'type' => PARAM_TEXT,
                'default' => '/',
            ],
            'custom_name' => [
                'type' => PARAM_PATH,
            ],
            'lastaccessed' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'accesscount' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'isbroken' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
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
            $targetdomains = explode("\n", $whitelist);
            if (count($targetdomains) > 0) {
                $ok = false;
                foreach ($targetdomains as $targetdomain) {
                    if (strpos($url, trim($targetdomain)) !== false) {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    return new lang_string('invaliddomain', 'local_customurls', [
                            'domains' => join(", ", $targetdomains),
                            'url' => $url,
                        ]
                    );
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
        $checkurl = get_config('local_customurls', 'checkurl');
        if ($checkurl) {
            if (!api::url_exists($url)) {
                return new lang_string('urlnotexists', 'local_customurls');
            }
        }

        return true;
    }

    /**
     * Validate customname
     *
     * @param string $customname
     * @return lang_string | bool
     */
    protected function validate_custom_name($customname) {
        // Do not allow url type chars ?&%=# or spaces).
        if (preg_match('/[\?&%=# ]/', $customname, $matches) !== 0) {
            return new lang_string('invalidcharsincustomname', 'local_customurls');
        }

        $currentid = self::get('id');
        // If this is a new record, and the custom name exists, then reject.
        if ($currentid == 0 && static::record_exists_select('custom_name = ?', [$customname])) {
            return new lang_string('duplicate_customname', 'local_customurls');
        }

        // If this is an existing record, and the id is not the same as this one, then reject it.
        $records = static::get_records(['custom_name' => $customname]);
        foreach ($records as $record) {
            if ($record->get('id') != $currentid) {
                return new lang_string('duplicate_customname', 'local_customurls');
            }
        }

        return true;
    }

    /**
     * Reset the count for one or all records
     *
     * @param int $id
     * @return void
     */
    public static function reset_count($id = 0) {
        global $DB, $USER;
        $context = context_system::instance();
        require_capability('local/customurls:managecustomurls', $context);
        $params = [
            'usermodified' => $USER->id,
            'timemodified' => time(),
        ];
        $where = '1 = 1';
        // If id is 0, then it's a reset all.
        if ($id > 0) {
            $params['id'] = $id;
            $where = 'id = :id';
        }
        $sql = "UPDATE {local_customurls} SET
            accesscount = 0, usermodified = :usermodified, timemodified = :timemodified WHERE $where";
        $DB->execute($sql, $params);
    }
}
