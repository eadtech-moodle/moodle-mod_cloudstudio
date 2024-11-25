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
     * Function get_url
     *
     * @return mixed|string
     */
    public static function get_url() {
        $config = get_config("cloudstudio");
        $url = trim($config->urlcloudstidio);
        if (!preg_match('/^https?:/', $url)) {
            $url = "http://{$url}";
        }
        $url = parse_url($url, PHP_URL_HOST);

        if ($url != $config->urlcloudstidio) {
            set_config("urlcloudstidio", $url, "cloudstudio");
        }

        return $url;
    }

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
     * Call for get player code.
     *
     * @param int $cmid
     * @param string $identifier
     * @param string $safetyplayer
     *
     * @return string
     * @throws \dml_exception
     */
    public static function getplayer($cmid, $identifier) {
        global $USER, $CFG;

        $config = get_config("cloudstudio");

        $safetyplayer = "";
        if ($config->safety && $config->safety != "none") {
            $safety = $config->safety;
            if (strpos($safety, "profile") === 0) {
                $safety = str_replace("profile_", "", $safety);
                $safetyplayer = $USER->profile->$safety;
            } else {
                $safetyplayer = $USER->$safety;
            }
        }

        $payload = [
            "identifier" => $identifier,
            "aluno_matricula" => $cmid,
            "aluno_nome" => fullname($USER),
            "aluno_email" => $USER->email,
            "safetyplayer" => $safetyplayer,
            "referer" => $CFG->wwwroot,
        ];

        return self::get("Player/{$identifier}/html", $payload);
    }

    /**
     * Call for list videos in cloudstudio.
     *
     * @param int $page
     * @param int $pasta
     * @param string $titulo
     *
     * @return array
     */
    public static function listing($page, $pasta, $search = "", $extensions = []) {
        $params = array(
            "page" => $page,
            "pastaid" => $pasta,
            "titulo" => $search,
            "extensions" => $extensions,
        );

        $json = self::get("arquivo", $params);
        return json_decode($json);
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

            $url = self::get_url();
            $result = $curl->get("https://{$url}/api/v1/{$metodth}?{$params}");

            $cache->set("{$metodth}-{$params}", $result);
            return $result;
        }

        return false;
    }
}
