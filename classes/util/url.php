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
 * External implementation for mod_cloudstudio.
 */

namespace mod_cloudstudio\util;

class url {

    public $videoid = false;
    public $engine = "";
    public $extra = "";

    public static function parse($videourl) {
        $url = new url();

        if (preg_match('/^[A-Z_\-]{5,150}$/', $videourl)) {
            $url->videoid = $videourl;
            $url->engine = "cloudstudio";
            return $url;
        }
        if (strpos($videourl, "cloudstudio.com") > 1) {
            if (preg_match('/\/\w+\/\w+\/([A-Z0-9\-\_]{3,255})/', $videourl, $path)) {
                $url->videoid = $path[1];
                $url->engine = "cloudstudio";
                return $url;
            }
        }
        if (strpos($videourl, "[resource-file") === 0) {
            $item = substr($videourl, 0, -1);
            $url->videoid = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $url->engine = "resource";
            return $url;
        }

        $url->videoid = false;
        $url->engine = "";
        return $url;
    }
}
