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
 * Mobile api
 *
 * @package mod_cloudstudio
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cloudstudio\output;

use mod_cloudstudio;

/**
 * Class mobile
 *
 * @package mod_cloudstudio\output
 */
class mobile {

    /**
     * Function mobile_course_view
     *
     * @param $args
     *
     * @return array
     * @throws \dml_exception
     */
    public static function mobile_course_view($args) {
        global $CFG, $OUTPUT, $USER;

        $cmid = $args['cmid'];
        $token = self::create_embed_token($USER->id);

        $t=time();
        $url = "{$CFG->wwwroot}/mod/cloudstudio/view.php?mobile=1&id={$cmid}&user_id={$USER->id}&secret={$token}&t={$t}" ;
        return [
            'templates' => [[
                'id' => 'main',
                'html' =>
                    '<iframe id="cloudstudio_iframe"
                             style="width:100%;height:100%;"
                             frameborder="0" allowfullscreen
                             sandbox="allow-scripts allow-same-origin allow-forms"
                             allow=":encrypted-media; :picture-in-picture; microphone; camera"
                             src="' . $url . '"></iframe>',
            ]]
        ];
    }

    /**
     * Function create_embed_token
     *
     * @param $userid
     *
     * @return bool|string
     * @throws \dml_exception
     */
    private static function create_embed_token($userid) {
        global $DB;

        $secret = md5(uniqid(0)) . md5(uniqid(1));
        $token = substr($secret, 0, rand(54, 64));

        $data = (object)[
            'user_id' => $userid,
            'secret' => $token,
            'created_at' => time()
        ];
        $DB->insert_record('cloudstudio_auth', $data);

        return $token;
    }

    /**
     * Function valid_token
     *
     * @param $userid
     * @param $secret
     *
     * @return bool
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function valid_token($userid, $secret) {
        global $DB;

        // Delete expired.
        $where = ['threshold' => time() - 60];
        $DB->delete_records_select('cloudstudio_auth', 'created_at < :threshold', $where);

        $auth = $DB->get_record('cloudstudio_auth', [
            'user_id' => $userid,
            'secret' => $secret,
        ]);

        if ($auth) {
            $user = get_complete_user_data('id', $userid);
            complete_user_login($user);

            return true;
        }

        return false;
    }
}
