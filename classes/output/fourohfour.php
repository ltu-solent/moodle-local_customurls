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

/**
 * 404 page content
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fourohfour implements renderable, templatable {

    /**
     * {@inheritDoc}
     *
     * @param renderer_base $output
     * @return stdClass Context data
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $PAGE;
        $config = get_config('local_customurls');
        $requesturi = $_SERVER['REQUEST_URI'];
        $fullurl = new moodle_url($requesturi);
        $data = new stdclass();
        $data->fullurl = $fullurl->out();
        $mainadmin = get_admin();
        $data->contactemail = $mainadmin->email;
        $data->contactname = fullname($mainadmin);
        if (empty($config->fourohfourmessage)) {
            $data->fourohfourmessage = get_string('requestedurlnotfound', 'local_customurls');
        } else {
            $data->fourohfourmessage = $config->fourohfourmessage;
        }

        if ($config->backgroundimage) {
            $data->backgroundurl = moodle_url::make_pluginfile_url(
                \context_system::instance()->id,
                'local_customurls',
                'customurls',
                null,
                null,
                $config->backgroundimage)->out();
        }

        if ($config->searchbox) {
            $courserenderer = $PAGE->get_renderer('core', 'course');
            $data->searchbox = $courserenderer->course_search_form('');
        }

        if (isloggedin() && $config->emailforloggedinusers) {
            $params = array('action' => 'sendmessage', 'requesturi' => $data->fullurl, 'sesskey' => sesskey());
            $messageurl = new moodle_url('/local/customurls/message.php', $params);
            $messagebutton = new \single_button($messageurl, get_string('tellus', 'local_customurls'), 'post', true);
            $data->messagehelp = get_string('messagehelp', 'local_customurls');
            $data->messagebtn = $OUTPUT->render($messagebutton);
        }

        return $data;
    }
}
