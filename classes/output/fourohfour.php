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
 * 404 output page
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

class fourohfour implements renderable, templatable {

    public function export_for_template(renderer_base $output) {
        $requesturi = $_SERVER['REQUEST_URI'];
        $fullurl = new moodle_url($requesturi);
        $data = new stdclass();
        $data->fullurl = $fullurl->out();
        $data->now = date ("m/d/Y h:i:s a");
        $mainadmin = get_admin();
        $data->contactemail = $mainadmin->email;
        $data->contactname = fullname($mainadmin);
        return $data;
    }
}
