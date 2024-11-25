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
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once("$CFG->libdir/resourcelib.php");

    $title = get_string('urlcloudstidio', 'mod_cloudstudio');
    $description = get_string('urlcloudstidio_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/urlcloudstidio',
        $title, $description, "", PARAM_URL));

    $title = get_string('token', 'mod_cloudstudio');
    $description = get_string('token_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/token',
        $title, $description, "", PARAM_TEXT));

    $title = get_string('showmapa', 'mod_cloudstudio');
    $description = get_string('showmapa_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configcheckbox('cloudstudio/showmapa',
        $title, $description, 1));

    $title = get_string('maxwidth', 'mod_cloudstudio');
    $description = get_string('maxwidth_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configtext('cloudstudio/maxwidth',
        $title, $description, 0, PARAM_INT));

    $title = get_string('openpopup', 'mod_cloudstudio');
    $description = get_string('openpopup_desc', 'mod_cloudstudio');
    $settings->add(new admin_setting_configcheckbox('cloudstudio/display_popup',
        $title, $description, 1));

    $itensseguranca = [
        "none" => get_string("safety_none", "cloudstudio"),
        "id" => get_string("safety_id", "cloudstudio"),
    ];
    $infofields = $DB->get_records("user_info_field");
    foreach ($infofields as $infofield) {
        $itensseguranca["profile_{$infofield->id}"] = $infofield->name;
    }
    $setting = new admin_setting_configselect("cloudstudio/safety",
        get_string("safety_title", "cloudstudio"),
        get_string("safety_desc", "cloudstudio"), "id",
        $itensseguranca
    );
    $settings->add($setting);
}
