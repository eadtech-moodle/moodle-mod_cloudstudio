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
 * Backup files
 *
 * @package    mod_cloudstudio
 * @category   backup
 * @copyright  2024 Eduardo kraus (http://eduardokraus.com)
 * @license    https://www.eduardokraus.com/
 */

/**
 * Define the complete cloudstudio structure for backup, with file and id annotations
 *
 * @package    mod_cloudstudio
 * @category   backup
 * @copyright  2024 Eduardo kraus (http://eduardokraus.com)
 * @license    https://www.eduardokraus.com/
 */
class backup_cloudstudio_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the cloudstudio instance.
        $cloudstudio = new backup_nested_element('cloudstudio', ['id'],
            ['course', 'name', 'intro', 'introformat', 'identificador', 'grade_approval']);

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $cloudstudio->set_source_table('cloudstudio', ['id' => backup::VAR_ACTIVITYID]);

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $cloudstudio->annotate_files('mod_cloudstudio', 'intro', null);

        // Return the root element (cloudstudio), wrapped into standard activity structure.
        return $this->prepare_activity_structure($cloudstudio);
    }
}
