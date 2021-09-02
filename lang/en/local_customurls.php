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

$string['blockedurl'] = 'The url ({$a}) has been blocked by the Moodle administrator.';

$string['confirmdelete'] = 'Confirm deletion of {$a}';
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

$string['deleted'] = '"{$a}" has been deleted.';
$string['deleteduser'] = 'Deleted user';
$string['description'] = 'Description';

$string['editcustomurl'] = 'Edit CustomUrl';

$string['info'] = 'Description';
$string['info_help'] = 'A short description indicating what content is to be expected at the original url';
$string['invaliddomain'] = 'Your url is invalid, it must contain the "{$a}" domain.';
$string['invalidurl'] = 'Your url is not a real url.';

$string['lastaccessed'] = 'Last accessed';

$string['managecustomurls'] = 'Manage custom urls';

$string['newcustomurl'] = 'New CustomUrl';
$string['newsaved'] = 'New CustomUrl saved.';

$string['pagenotfound'] = '404 - Page not found';
$string['pluginname'] = "Custom Urls";

$string['requestedurlnotfound'] = '<p>The requested URL (<a href="{$a}">{$a}</a>) was not found on this server.</p>' .
    '<p>If you entered the URL manually please check your spelling and try again.</p>';

$string['redirectto'] = 'Redirect to';

$string['unclean'] = 'Unclean url';
$string['updated'] = '"{$a}" has been updated.';
$string['url'] = 'Original Url';
$string['url_help'] = 'The url you wish to direct the user to. Please include the full domain name.';
$string['urlnotexists'] = 'The url doesn\'t exist';

$string['whitelist'] = 'Whitelist';
$string['whitelist_help'] = 'Only these domains are permitted';
$string['whitelistdomainpattern'] = 'Whitelist domain pattern';
$string['whitelistdomainpattern_desc'] = 'Full domain or part domain matching allowable domain patterns. Default any domain.';