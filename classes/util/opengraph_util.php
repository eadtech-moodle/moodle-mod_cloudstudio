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
 * User: Eduardo Kraus
 * Date: 09/08/2023
 * Time: 17:42
 */

namespace mod_cloudstudio\util;

class opengraph_util {

    /**
     * Holds all the Open Graph values we've parsed from a page
     *
     */
    private $values = array();

    /**
     * Fetches a URI and parses it for Open Graph data, returns
     * false on error.
     *
     * @param String $uri URI to page to parse for Open Graph data
     * @return opengraph_util
     */
    public static function fetch($uri) {
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['action: opengraph']);

        $html = curl_exec($ch);

        curl_close($ch);

        if (!empty($html)) {
            return self::parse($html);
        } else {
            return null;
        }
    }

    /**
     * Parses HTML and extracts Open Graph data, this assumes
     * the document is at least well formed.
     *
     * @param String $html HTML to parse
     * @return opengraph_util
     */
    private static function parse($html) {

        $opengraphutil = new opengraph_util();

        preg_match_all('/property="og:(\w+|video:\w+)"\s+content="(.*?)"/', $html, $output);
        foreach ($output[1] as $key => $value) {
            $opengraphutil->values[$value] = $output[2][$key];
        }

        preg_match_all('/content="(.*?)"\s+property="og:(\w+|video:\w+)"/', $html, $output);
        foreach ($output[1] as $key => $value) {
            $opengraphutil->values[$value] = $output[2][$key];
        }

        return $opengraphutil;
    }

    /**
     * Helper method to access attributes directly
     * Example:
     * $graph->title
     *
     * @param String $key Key to fetch from the lookup
     * @return int|mixed|string
     */
    public function get($key) {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        return null;
    }

}