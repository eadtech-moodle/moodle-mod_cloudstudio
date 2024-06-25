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
 * setting file
 *
 * @package    mod_cloudstudio
 * @copyright  2023 Eduardo kraus (http://eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once("$CFG->libdir/resourcelib.php");

    $title = get_string('urlcloudstidio', 'mod_cloudstudio');
    $description = get_string('urlcloudstidio_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/urlcloudstidio',
        $title, $description, "", PARAM_URL));

    $title = get_string('tokencloudstidio', 'mod_cloudstudio');
    $description = get_string('tokencloudstidio_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/tokencloudstidio',
        $title, $description, "", PARAM_TEXT));

    $title = get_string('showmapa', 'mod_cloudstudio');
    $description = get_string('showmapa_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configcheckbox('cloudstudio/showmapa',
        $title, $description, 1));

    $options = array(
        0 => get_string('settings_opcional_desmarcado', 'mod_cloudstudio'),
        1 => get_string('settings_opcional_marcado', 'mod_cloudstudio'),
        2 => get_string('settings_obrigatorio_desmarcado', 'mod_cloudstudio'),
        3 => get_string('settings_obrigatorio_marcado', 'mod_cloudstudio'),
    );

    $title = get_string('showcontrols', 'mod_cloudstudio');
    $description = get_string('showcontrols_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configselect('cloudstudio/showcontrols',
        $title, $description, 1, $options));

    $title = get_string('autoplay', 'mod_cloudstudio');
    $description = get_string('autoplay_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configselect('cloudstudio/autoplay',
        $title, $description, 0, $options));

    $title = get_string('maxwidth', 'mod_cloudstudio');
    $description = get_string('maxwidth_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/maxwidth',
        $title, $description, 0, PARAM_INT));
}
