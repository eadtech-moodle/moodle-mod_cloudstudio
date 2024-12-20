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
 * Progress Service
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\external;

use external_function_parameters;
use external_single_structure;
use mod_cloudstudio\local\analytics\cloudstudio_view;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Progress Service
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class progress extends \external_api {
    /**
     * Describes the parameters for save
     *
     * @return external_function_parameters
     */
    public static function save_parameters() {
        return new \external_function_parameters([
            'view_id' => new \external_value(PARAM_INT, 'The instance id', VALUE_REQUIRED),
            'currenttime' => new \external_value(PARAM_INT, 'The current time', VALUE_REQUIRED),
            'duration' => new \external_value(PARAM_INT, 'The duration', VALUE_REQUIRED),
            'percent' => new \external_value(PARAM_INT, 'The percent', VALUE_REQUIRED),
            'mapa' => new \external_value(PARAM_RAW, 'The mapa', VALUE_REQUIRED),
        ]);
    }

    /**
     * Record watch time
     *
     * @param int $viewid
     * @param int $currenttime
     * @param int $duration
     * @param int $percent
     *
     * @param     $mapa
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function save($viewid, $currenttime, $duration, $percent, $mapa) {
        global $DB;

        $cloudstudioview = $DB->get_record("cloudstudio_view", ["id" => $viewid]);
        if ($cloudstudioview) {
            $context = \context_module::instance($cloudstudioview->cm_id);
            self::validate_context($context);
            require_capability('mod/cloudstudio:view', $context);

            $params = self::validate_parameters(self::save_parameters(), [
                'view_id' => $viewid,
                'currenttime' => $currenttime,
                'duration' => $duration,
                'percent' => $percent,
                'mapa' => $mapa,
            ]);
            $viewid = $params['view_id'];
            $currenttime = $params['currenttime'];
            $duration = $params['duration'];
            $percent = $params['percent'];

            cloudstudio_view::update($viewid, $currenttime, $duration, $percent, $mapa);
            return ['success' => true, 'exec' => "OK"];
        }
        return ['success' => false, 'exec' => "notFound"];
    }

    /**
     * Describes the save return value.
     *
     * @return external_single_structure
     */
    public static function save_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL),
            'exec' => new \external_value(PARAM_RAW),
        ]);
    }
}
