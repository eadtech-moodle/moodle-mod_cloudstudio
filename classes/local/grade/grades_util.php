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
 * Grades implementation for mod_cloudstudio.
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\local\grade;

/**
 * Class grades_util
 *
 * @package mod_cloudstudio\local\grade
 */
class grades_util {

    /**
     * Function update
     *
     * @param $cmid
     * @param $percent
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function update($cmid, $percent) {
        global $DB, $CFG, $USER;

        require_once("{$CFG->libdir}/completionlib.php");

        $cm = get_coursemodule_from_id('cloudstudio', $cmid, 0, false, MUST_EXIST);
        $course = get_course($cm->course);
        $cloudstudio = $DB->get_record('cloudstudio', ['id' => $cm->instance], '*', MUST_EXIST);

        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            if ($percent >= $cloudstudio->completionpercent) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }
        }

        if ($cloudstudio->grade_approval == 1) {
            $grade = [
                "userid" => $USER->id,
                "rawgrade" => $percent,
            ];

            require_once("{$CFG->libdir}/gradelib.php");
            $grades = grade_get_grades($course->id, 'mod', 'cloudstudio', $cloudstudio->id, $USER->id);
            if (isset($grades->items[0]->grades)) {
                foreach ($grades->items[0]->grades as $gradeitem) {
                    if (intval($percent) > intval($gradeitem->grade)) {
                        self::grade_item_update($cloudstudio, $grade);
                        break;
                    }
                }
            }
        }
    }

    /**
     * Function grade_item_update
     *
     * @param $cloudstudio
     * @param null $grades
     *
     * @return int
     */
    public static function grade_item_update($cloudstudio, $grades = null) {
        global $CFG;

        require_once("{$CFG->dirroot}/lib/gradelib.php");

        if (!defined('GRADE_TYPE_VALUE')) {
            define('GRADE_TYPE_VALUE', 1);
        }

        $params = [
            'itemname' => $cloudstudio->name,
            'gradetype' => GRADE_TYPE_VALUE,
            'grademax' => 100,
            'grademin' => 0,
        ];

        if (isset($cloudstudio->cmidnumber)) {
            $params['idnumber'] = $cloudstudio->cmidnumber;
        }

        if ($grades === 'reset') {
            $params['reset'] = true;
            $grades = null;
        }

        return grade_update('mod/cloudstudio', $cloudstudio->course, 'mod', 'cloudstudio', $cloudstudio->id, 0, $grades, $params);
    }
}
