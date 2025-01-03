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
 * Custom completion
 *
 * @package   mod_cloudstudio
 * @copyright 2024 EadTech {@link https://www.eadtech.com.br}
 * @author    2024 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_cloudstudio\completion;

use core_completion\activity_custom_completion;


/**
 * Class custom_completion
 *
 * @package mod_cloudstudio\completion
 */
class custom_completion extends activity_custom_completion {

    /**
     * Function get_state
     *
     * @param string $rule
     *
     * @return int
     * @throws \dml_exception
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $userentries = $DB->get_field("cloudstudio_view", "MAX(percent)", ["cm_id" => $this->cm->id, "user_id" => $this->userid]);
        $completionpercent = $this->cm->customdata['customcompletionrules']['completionpercent'];

        return ($completionpercent <= $userentries) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completionpercent'];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_custom_rule_descriptions(): array {
        $entries = $this->cm->customdata['customcompletionrules']['completionpercent'] ?? 0;
        return [
            'completionpercent' => get_string('completiondetail:completionpercent', 'mod_cloudstudio', $entries),
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionusegrade',
            'completionpassgrade',
            'completionpercent',
        ];
    }
}
