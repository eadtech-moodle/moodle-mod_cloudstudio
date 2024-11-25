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
 * Cloudstudio api
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\local\util;

/**
 * Class cloudstudio_api
 *
 * @package mod_cloudstudio\util
 */
class cloudstudio_api {

    /**
     * Function identificador
     *
     * @param $identificador
     *
     * @return mixed
     */
    public static function identificador($identificador) {
        return str_replace("-", "_", $identificador);
    }

    /**
     * Function get
     *
     * @param $metodth
     * @param array $params
     *
     * @return bool|mixed
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function get($metodth, $params = []) {
        $params = http_build_query($params, '', '&');

        $config = get_config('cloudstudio');
        $cache = \cache::make('mod_cloudstudio', 'cloudstudio_api_get');
        if ($cache->has("{$metodth}-{$params}-{$config->token}")) {
            return $cache->get("{$metodth}-{$params}");
        }

        if (isset($config->urlcloudstidio[10]) && isset($config->token[10])) {
            $curl = new \curl();
            $curl->setopt([
                'CURLOPT_HTTPHEADER' => [
                    "Authorization: {$config->token}",
                ],
            ]);
            $result = $curl->get("{$config->urlcloudstidio}api/v1/{$metodth}?{$params}");

            $cache->set("{$metodth}-{$params}", $result);
            return $result;
        }

        return false;
    }
}
