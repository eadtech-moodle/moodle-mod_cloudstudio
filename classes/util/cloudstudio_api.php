<?php
/**
 * User: Eduardo Kraus
 * Date: 26/06/2024
 * Time: 16:16
 */

namespace mod_cloudstudio\util;

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
     */
    public static function get($metodth, $params = []) {
        $params = http_build_query($params, '', '&');

        $cache = \cache::make('mod_cloudstudio', 'cloudstudio_api_get');
        if ($cache->has("{$metodth}-{$params}")) {
            return $cache->get("{$metodth}-{$params}");
        }

        $config = get_config('cloudstudio');
        if (isset($config->urlcloudstidio[10]) && isset($config->tokencloudstidio[10])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "{$config->urlcloudstidio}api/v1/{$metodth}?{$params}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: {$config->tokencloudstidio}"]);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                return false;
            }

            $cache->set("{$metodth}-{$params}", $result);
            return $result;
        }

        return false;
    }
}
