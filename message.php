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
 * 404 page
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login(0, true);

required_param('sesskey', PARAM_RAW);
$requesturi = required_param('requesturi', PARAM_URL);
$action = required_param('action', PARAM_ALPHA);

$PAGE->set_context ( context_system::instance () );
$PAGE->set_pagelayout ('base');
$PAGE->set_title (get_string('pagenotfound', 'local_customurls'));
$PAGE->set_heading (get_string('pagenotfound', 'local_customurls'));
$PAGE->set_url (new moodle_url('/message.php'));

echo $OUTPUT->header();
$customurlsconfig = get_config('local_customurls');
$adminuser = get_admin();
$sendto = $adminuser;
if (!empty($customurlsconfig->contactemail)) {
    $sendto = \core_user::get_user_by_email($customurlsconfig->contactemail);
    if (!$sendto) {
        $sendto = $adminuser;
    }
}

$messagetext = get_string('emailmessage', 'local_customurls', (object)['url' => $requesturi, 'fullname' => fullname($USER)]);
$subject = get_string('emailsubject', 'local_customurls');

$message = new \core\message\message();
$message->courseid          = $SITE->id;
$message->component         = 'local_customurls';
$message->name              = 'notifymissingpage';
$message->userfrom          = $USER->id;
$message->replyto           = $USER->email;
$message->replytoname       = fullname($USER);
$message->subject           = $subject;
$message->fullmessageformat = FORMAT_HTML;
$message->userto            = $sendto;
$message->fullmessagehtml   = text_to_html($messagetext);
$message->fullmessage       = $messagetext;

if (message_send($message)) {
    echo $OUTPUT->notification(get_string('thankyou', 'local_customurls'), \core\output\notification::NOTIFY_INFO);
}

if ($customurlsconfig->searchbox) {
    $courserenderer = $PAGE->get_renderer('core', 'course');
    echo $courserenderer->course_search_form('');
}

echo $OUTPUT->footer();
