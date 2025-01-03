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
 * @package   mod_cloudstudio
 * @category  backup
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   https://www.eduardokraus.com/
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/cloudstudio/backup/moodle2/restore_cloudstudio_stepslib.php');

/**
 * Restore task for the cloudstudio activity module
 *
 * Provides all the settings and steps to perform complete restore of the activity.
 *
 * @package   mod_cloudstudio
 * @category  backup
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   https://www.eduardokraus.com/
 */
class restore_cloudstudio_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // We have just one structure step here.
        $this->add_step(new restore_cloudstudio_activity_structure_step('cloudstudio_structure', 'cloudstudio.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = [];

        $contents[] = new restore_decode_content('cloudstudio', ['intro'], 'cloudstudio');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = [];

        $rules[] = new restore_decode_rule('CLOUDSTUDIOVIEWBYID', '/mod/cloudstudio/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CLOUDSTUDIOINDEX', '/mod/cloudstudio/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * cloudstudio logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        $rules = [];

        $rules[] = new restore_log_rule('cloudstudio', 'add', 'view.php?id={course_module}', '{cloudstudio}');
        $rules[] = new restore_log_rule('cloudstudio', 'update', 'view.php?id={course_module}', '{cloudstudio}');
        $rules[] = new restore_log_rule('cloudstudio', 'view', 'view.php?id={course_module}', '{cloudstudio}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];

        $rules[] = new restore_log_rule('cloudstudio', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
