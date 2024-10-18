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
 * Cloudstudio Service
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\service;

use mod_cloudstudio\local\util\cloudstudio_api;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Cloudstudio Service
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cloudstudio extends \external_api {
    /**
     * Describes the parameters for save
     *
     * @return \external_function_parameters
     */
    public static function files_parameters() {
        return new \external_function_parameters([
            'path' => new \external_value(PARAM_INT, 'The path', VALUE_OPTIONAL),
            'page' => new \external_value(PARAM_INT, 'The page', VALUE_OPTIONAL),
            'titulo' => new \external_value(PARAM_TEXT, 'The title', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Record watch time
     *
     * @param int $path
     * @param int $page
     * @param string $titulo
     *
     * @return array
     * @throws \dml_exception*@throws \required_capability_exception
     * @throws \required_capability_exception
     */
    public static function files($path = 0, $page = 0, $titulo = "") {
        global $USER;

        $context = \context_user::instance($USER->id);
        require_capability('mod/cloudstudio:addinstance', $context);

        $config = get_config('cloudstudio');
        if (isset($config->urlcloudstidio[10]) && isset($config->tokencloudstidio[10])) {

            $result = cloudstudio_api::get("Arquivo", [
                "titulo" => $titulo,
                "pasta_id" => $path,
                "page" => $page,
            ]);

            if ($result === false) {
                return ['videos' => []];
            }

            $files = json_decode($result);
            return $files;
        }

        return ['videos' => []];
    }

    /**
     * Describes the save return value.
     *
     * @return \external_single_structure
     */
    public static function files_returns() {
        return new \external_single_structure([
            'videos' => new \external_multiple_structure(
                new \external_single_structure([
                    'url' => new \external_value(PARAM_RAW, 'url'),
                    'identificador' => new \external_value(PARAM_RAW, 'identificador'),
                    'filename' => new \external_value(PARAM_RAW, 'filename'),
                    'titulo' => new \external_value(PARAM_RAW, 'titulo'),
                    'descricao' => new \external_value(PARAM_RAW, 'descricao'),
                    'formato' => new \external_value(PARAM_RAW, 'formato'),
                    'duracao' => new \external_value(PARAM_RAW, 'duracao'),
                    'size' => new \external_value(PARAM_RAW, 'size'),
                    'bytes' => new \external_value(PARAM_RAW, 'bytes'),
                    'data' => new \external_value(PARAM_RAW, 'data'),
                    'thumb' => new \external_value(PARAM_RAW, 'thumb'),
                ])),
        ]);
    }
}
