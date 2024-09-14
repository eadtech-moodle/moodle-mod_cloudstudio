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
 * Class completion_util
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\completion;

/**
 * Class completion_util
 *
 * @package mod_cloudstudio\completion
 */
class completion_util {

    /**
     * Function get_completion_state
     *
     * @param $course
     * @param $cm
     * @param $userid
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function get_completion_state($course, $cm, $userid) {
        global $CFG, $DB, $USER;

        $cloudstudio = $DB->get_record('cloudstudio', ['id' => $cm->instance], '*', MUST_EXIST);
        if ($cloudstudio->completionpercent) {

            require_once($CFG->libdir . '/gradelib.php');
            $grades = grade_get_grades($course->id, 'mod', 'cloudstudio', $cloudstudio->id, $USER->id);

            if (isset($grades->items[0]->grades)) {
                foreach ($grades->items[0]->grades as $grade) {
                    if (intval($cloudstudio->completionpercent) >= intval($grade->grade)) {
                        return true;
                    }
                }
            }

            return false;
        }

        return true;
    }
}
