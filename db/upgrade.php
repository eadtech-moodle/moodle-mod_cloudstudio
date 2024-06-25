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
 * @package    mod_cloudstudio
 * @copyright  2023 Eduardo kraus (http://eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * function xmldb_cloudstudio_upgrade
 *
 * @param int $oldversion
 *
 * @return bool
 * @throws ddl_exception
 * @throws ddl_field_missing_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_cloudstudio_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019010303) {

        $tablecloudstudio = new xmldb_table('cloudstudio');

        $fieldurl = new xmldb_field('cloudstudioid', XMLDB_TYPE_CHAR, 255);
        if ($dbman->field_exists($tablecloudstudio, $fieldurl)) {
            $dbman->rename_field($tablecloudstudio, $fieldurl, 'url');
        }

        upgrade_plugin_savepoint(true, 2019010303, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023032506) {

        $tablecloudstudioview = new xmldb_table('cloudstudio_view');

        $tablecloudstudioview->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $tablecloudstudioview->add_field('cm_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('currenttime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('duration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('percent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('mapa', XMLDB_TYPE_CHAR, null, null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL);
        $tablecloudstudioview->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL);

        $tablecloudstudioview->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($tablecloudstudioview)) {
            $dbman->create_table($tablecloudstudioview);
        }

        $tablecloudstudio = new xmldb_table('cloudstudio');

        $fieldgradeapproval = new xmldb_field('grade_approval', XMLDB_TYPE_INTEGER, 10);
        if (!$dbman->field_exists($tablecloudstudio, $fieldgradeapproval)) {
            $dbman->add_field($tablecloudstudio, $fieldgradeapproval);
        }

        $fieldcompletpercent = new xmldb_field('completionpercent', XMLDB_TYPE_INTEGER, 10);
        if (!$dbman->field_exists($tablecloudstudio, $fieldcompletpercent)) {
            $dbman->add_field($tablecloudstudio, $fieldcompletpercent);
        }

        upgrade_plugin_savepoint(true, 2023032506, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023052000) {

        // Add auth table.
        $table = new xmldb_table('cloudstudio_auth');

        // Add fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL);
        $table->add_field('secret', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL);

        // Add keys and index.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Create table if it does not exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2023052000, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023071800) {

        $table = new xmldb_table('cloudstudio');
        $field = new xmldb_field("showshowinfo", XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, 0, 'showcontrols');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'showinfo');
        }

        upgrade_plugin_savepoint(true, 2023071800, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023072700) {

        $table = new xmldb_table('cloudstudio');
        $field = new xmldb_field("complet_percent", XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, 0, 'grade_approval');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'completionpercent');
        }

        upgrade_plugin_savepoint(true, 2023072700, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023080701) {

        $table = new xmldb_table('cloudstudio');

        $index = new xmldb_index('showrel', XMLDB_INDEX_NOTUNIQUE, array('showrel'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field("showrel", XMLDB_TYPE_INTEGER, 10);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2023080701, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023081100) {

        $table = new xmldb_table('cloudstudio');

        $index = new xmldb_index('showinfo', XMLDB_INDEX_NOTUNIQUE, array('showinfo'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field("showinfo", XMLDB_TYPE_INTEGER, 10);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2023081100, 'mod', 'cloudstudio');
    }

    if ($oldversion < 2023081602) {

        $table = new xmldb_table('cloudstudio');

        $field1 = new xmldb_field("playersize", XMLDB_TYPE_CHAR, 15, null, false, false, "", "videourl");
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);

            $sql = "UPDATE {cloudstudio} SET playersize = videosize";
            $DB->execute($sql);
        }

        $index = new xmldb_index('videosize', XMLDB_INDEX_NOTUNIQUE, array('videosize'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field2 = new xmldb_field("videosize", XMLDB_TYPE_CHAR, 15);
        if ($dbman->field_exists($table, $field2)) {
            $dbman->drop_field($table, $field2);
        }

        upgrade_plugin_savepoint(true, 2023081602, 'mod', 'cloudstudio');
    }

    return true;
}
