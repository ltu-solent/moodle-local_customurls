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
 * Customurl edit/create form
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_customurls\forms;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

use core\form\persistent;
use html_writer;
use lang_string;

/**
 * Customurl form
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customurl_form extends persistent {

    /**
     * Cross reference for the object this form is working from.
     *
     * @var string
     */
    protected static $persistentclass = 'local_customurls\\customurl';

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        global $CFG, $USER;
        $mform = $this->_form;
        $config = get_config('local_customurls');

        $mform->addElement('text', 'url', new lang_string('url', 'local_customurls'), 'size="51"');
        $mform->addRule('url', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('url', 'url', 'local_customurls');

        $whitelist = explode("\n", trim($config->whitelistdomainpattern));
        if (!empty($config->whitelistdomainpattern) && count($whitelist) > 0) {
            $whitelisthtml = html_writer::start_tag('ul');
            foreach ($whitelist as $item) {
                if (!empty($item)) {
                    $whitelisthtml .= html_writer::tag('li', $item);
                }
            }
            $whitelisthtml .= html_writer::end_tag('ul');
            $mform->addElement('static', 'whitelist', new lang_string('whitelist', 'local_customurls'), $whitelisthtml);
            $mform->addHelpButton('whitelist', 'whitelist', 'local_customurls');
        }

        $mform->addElement('text', 'custom_name', new lang_string('custompath', 'local_customurls'), 'size="51"');
        $mform->addRule('custom_name', new lang_string('required'), 'required', null, 'client');
        $mform->addHelpButton('custom_name', 'custom_name', 'local_customurls');

        $mform->addElement('textarea', 'info', new lang_string('description', 'local_customurls'),
            'wrap="virtual" rows="5" cols="50"');
        $mform->addRule('info', new lang_string('required'), 'required', null, 'client');
        $mform->addHelpButton('info', 'info', 'local_customurls');

        $mform->addElement('checkbox', 'isbroken', new lang_string('isbroken', 'local_customurls'));
        $mform->addHelpButton('isbroken', 'isbroken', 'local_customurls');

        $this->add_action_buttons();
    }
}
