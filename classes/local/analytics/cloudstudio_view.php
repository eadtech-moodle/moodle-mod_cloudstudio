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

namespace mod_cloudstudio\local\analytics;

use mod_cloudstudio\local\grade\grades_util;

/**
 * Cloudstudio View implementation for mod_cloudstudio.
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cloudstudio_view {

    /**
     * Function create
     *
     * @param $cmid
     *
     * @return mixed|object
     * @throws \dml_exception
     */
    public static function create($cmid) {
        global $USER, $DB;

        $sql = "SELECT * FROM {cloudstudio_view} WHERE cm_id = :cm_id AND user_id = :user_id ORDER BY id DESC LIMIT 1";
        $cloudstudioview = $DB->get_record_sql($sql, ["cm_id" => $cmid, "user_id" => $USER->id]);

        if ($cloudstudioview) {
            if ($cloudstudioview->currenttime > ($cloudstudioview->duration - 3)) {
                return self::internal_create($cmid);
            }
            if ($cloudstudioview->percent < 90) {
                return $cloudstudioview;
            }
        }

        return self::internal_create($cmid);
    }

    /**
     * Function internal_create
     *
     * @param $cmid
     *
     * @return object
     */
    private static function internal_create($cmid) {
        global $USER, $DB;

        $cloudstudioview = (object)[
            "cm_id" => $cmid,
            "user_id" => $USER->id,
            "currenttime" => 0,
            "duration" => 0,
            "percent" => 0,
            "mapa" => "{}",
            "timecreated" => time(),
            "timemodified" => time(),
        ];

        try {
            $cloudstudioview->id = $DB->insert_record("cloudstudio_view", $cloudstudioview);
        } catch (\dml_exception $e) {
            return (object)['id' => 0];
        }

        return $cloudstudioview;
    }

    /**
     * Function update
     *
     * @param $viewid
     * @param $currenttime
     * @param $duration
     * @param $percent
     * @param $mapa
     *
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function update($viewid, $currenttime, $duration, $percent, $mapa) {
        global $DB, $USER, $CFG;

        $cloudstudioview = $DB->get_record('cloudstudio_view', ['id' => $viewid, "user_id" => $USER->id]);

        if ($cloudstudioview) {
            $cloudstudioview->currenttime = $currenttime;
            $cloudstudioview->duration = $duration;
            $cloudstudioview->percent = $percent;
            $cloudstudioview->mapa = $mapa;
            $cloudstudioview->timemodified = time();

            $status = $DB->update_record("cloudstudio_view", $cloudstudioview);

            require_once("{$CFG->dirroot}/mod/cloudstudio/classes/grade/grades_util.php");
            grades_util::update($cloudstudioview->cm_id, $percent);

            return $status;
        }
        return false;
    }
}
