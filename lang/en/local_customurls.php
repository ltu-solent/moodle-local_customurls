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
 * Language string file
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accesscount'] = 'Access count';
$string['actions'] = 'Actions';

$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'Background image behind the message area';
$string['blockedurl'] = 'The url ({$a}) has been blocked by the Moodle administrator.';
$string['broken'] = 'Broken url';

$string['checkurl'] = 'Check url';
$string['checkurl_desc'] = 'Check the url exists when saving or editing.';
$string['checkurls'] = 'Check urls';
$string['confirmdelete'] = 'Confirm deletion of {$a}';
$string['contactemail'] = 'Contact email';
$string['contactemail_desc'] = 'Email address to notify page is missing.';
$string['contactus'] = '<p>If you think this is a server error, please contact us
    at <a href="mailto:{$a->contactemail}">{$a->contactname}</a>.</p>';
$string['createdby'] = 'Created by';
$string['customlink'] = 'Custom link';
$string['custom_name'] = 'Custom link';
$string['custom_name_help'] = 'Redirect path for the original url';
$string['custompath'] = 'Custom path';
$string['customurls:managecustomurls'] = 'Can manage custom urls';
$string['customurls:librarycustomurls'] = 'Can view library urls';
$string['customurls:admincustomurls'] = 'Can view admin urls';
$string['customurlshelp'] = 'Extra help';
$string['customurlshelp_desc'] = 'Some extra help displayed on the custom urls edit page.';

$string['deleted'] = '"{$a}" has been deleted.';
$string['deleteduser'] = 'Deleted user';
$string['description'] = 'Description';

$string['editcustomurl'] = 'Edit CustomUrl';
$string['emailforloggedinusers'] = 'Display contact button (Logged in users)';
$string['emailforloggedinusers_desc'] = 'Display contact button (Logged in users)';
$string['emailforloggedoutusers'] = 'Display contact button (Logged out users)';
$string['emailforloggedoutusers_desc'] = 'Display contact button (Logged out users)';
$string['emailmessage'] = 'I wish to report that the following page doesn\'t exist: {$a->url}.

Thank you

{$a->fullname}';
$string['emailsubject'] = 'Missing page in Moodle';

$string['fourohfourmessage'] = '404 Messsage';
$string['fourohfourmessage_desc'] = 'The message you want to display to users';

$string['id'] = 'id';
$string['info'] = 'Description';
$string['info_help'] = 'A short description indicating what content is to be expected at the original url.
Please avoid using people\'s names here as this information is publicly available.';
$string['invaliddomain'] = 'Your url is invalid, it must contain one of the following domains: {$a}.';
$string['invalidurl'] = 'Your url is not a real url.';
$string['isbroken'] = 'Is broken?';
$string['isbroken_help'] = 'You can either manually indicate that a link is broken, or this will be set automatically by
a task that will check periodically.';

$string['lastaccessed'] = 'Last accessed';

$string['managecustomurls'] = 'Manage custom urls';
$string['messagehelp'] = 'If you believe this page should have existed, please let us know by clicking on the "Report broken link" button.
This will send our team a message. Thank you.';

$string['newcustomurl'] = 'New CustomUrl';
$string['newsaved'] = 'New CustomUrl saved.';

$string['pagenotfound'] = '404 - Page not found';
$string['pluginname'] = "Custom Urls";

$string['query'] = 'Search customurls';

$string['requestedurlnotfound'] = '<p>The requested URL was not found on this server.</p>' .
    '<p>If you entered the URL manually please check your spelling and try again.</p>';

$string['redirectto'] = 'Redirect to';

$string['searchbox'] = 'Display course search box';
$string['searchbox_desc'] = 'Display course search box on 404 page';
$string['statusbroken'] = 'Url is broken';
$string['statusok'] = 'Url is OK';

$string['tellus'] = 'Report broken link';
$string['thankyou'] = 'Thank you for reporting this broken link';

$string['unclean'] = 'Unclean url';
$string['updated'] = '"{$a}" has been updated.';
$string['url'] = 'Original Url';
$string['url_help'] = 'The url you wish to direct the user to. You do not need the full domain unless the site is external.';
$string['urlbroken'] = 'The url for {$a->custom_name} is broken.';
$string['urlunbroken'] = 'The url for {$a->custom_name} now appears to be working.';
$string['urlnotexists'] = 'The url doesn\'t exist';
$string['urlstatus'] = 'Url status';

$string['whitelist'] = 'Whitelist';
$string['whitelist_help'] = 'Only these domains are permitted';
$string['whitelistdomainpattern'] = 'Whitelist domain pattern';
$string['whitelistdomainpattern_desc'] = 'Full domain or part domain matching allowable domain patterns. Default any domain. One domain per line.';
