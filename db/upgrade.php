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
 * Upgrade file
 *
 * @package   local_customurls
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2021 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


function xmldb_local_customurls_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2012061924) {

        // Define field id to be added to customurls.
        $table = new xmldb_table('customurls');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Customurls savepoint reached.
        upgrade_plugin_savepoint(true, $oldversion, 'local', 'customurls');
    }

    if ($oldversion < 2021090103) {
        $table = new xmldb_table('customurls');
        $field = new xmldb_field('lastaccessed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('accesscount', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $index = new xmldb_index('customname', XMLDB_INDEX_UNIQUE, array('custom_name'));
        // NOTE: Check for duplicate customnames before upgrading as this may cause duplicate keys.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $field = new xmldb_field('userrole');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2021090103, 'local', 'customurls');
    }

    if ($oldversion < 2021090106) {
        $table = new xmldb_table('customurls');
        $index = new xmldb_index('url', XMLDB_INDEX_NOTUNIQUE, array('url'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $table = new xmldb_table('customurls');
        $field = new xmldb_field('isbroken', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    return $result;
}
