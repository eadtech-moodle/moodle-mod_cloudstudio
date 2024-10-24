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
 * services file
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_cloudstudio_external_progress_save' => [
        'classpath' => 'mod/cloudstudio/classes/external/progress.php',
        'classname' => 'mod_cloudstudio\external\progress',
        'methodname' => 'save',
        'description' => 'Save progress video.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_cloudstudio_external_cloudstudio_files' => [
        'classpath' => 'mod/cloudstudio/classes/external/cloudstudio.php',
        'classname' => 'mod_cloudstudio\external\cloudstudio',
        'methodname' => 'files',
        'description' => 'Get list files.',
        'type' => 'write',
        'ajax' => true,
    ],
];
