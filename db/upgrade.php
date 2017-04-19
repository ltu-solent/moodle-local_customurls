<?php
function xmldb_local_customurls_upgrade($oldversion = '2012061924') {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < $oldversion) {

        // Define field id to be added to customurls
        $table = new xmldb_table('customurls');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

        // Conditionally launch add field id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // customurls savepoint reached
        upgrade_plugin_savepoint(true, $oldversion, 'local', 'customurls');
    }


    return $result;
}
?>