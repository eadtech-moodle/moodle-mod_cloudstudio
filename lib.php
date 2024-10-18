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
 * lib file
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cloudstudio\util\cloudstudio_api;

/**
 * Function cloudstudio_supports
 *
 * @param $feature
 *
 * @return bool|int|null|string
 */
function cloudstudio_supports($feature) {

    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_COMMENT:
            return true;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case 'mod_purpose':
            return 'content';
        default:
            return null;
    }
}

/**
 * Function cloudstudio_update_grades
 *
 * @param stdClass $cloudstudio
 * @param int $userid
 * @param bool $nullifnone
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function cloudstudio_update_grades($cloudstudio, $userid = 0, $nullifnone = true) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    if ($cloudstudio->grade_approval) {
        if ($grades = cloudstudio_get_user_grades($cloudstudio, $userid)) {
            \mod_cloudstudio\grade\grades_util::grade_item_update($cloudstudio, $grades);
        }
    }
}

/**
 * Function cloudstudio_get_user_grades
 *
 * @param stdClass $cloudstudio
 * @param int $userid
 *
 * @return array|bool
 * @throws coding_exception
 * @throws dml_exception
 */
function cloudstudio_get_user_grades($cloudstudio, $userid = 0) {
    global $DB;

    if (!$cloudstudio->grade_approval) {
        return false;
    }

    $cm = get_coursemodule_from_instance('cloudstudio', $cloudstudio->id);

    $params = ['cm_id' => $cm->id];

    $extrawhere = ' ';
    if ($userid > 0) {
        $extrawhere .= ' AND user_id = :user_id';
        $params['user_id'] = $userid;
    }

    $sql = "SELECT user_id as userid, MAX(percent) as rawgrade
              FROM {cloudstudio_view}
             WHERE cm_id = :cm_id {$extrawhere}
             GROUP BY user_id";
    return $DB->get_records_sql($sql, $params);
}


/**
 * Function cloudstudio_add_instance
 *
 * @param stdClass $cloudstudio
 * @param mod_cloudstudio_mod_form|null $mform
 *
 * @return bool|int
 * @throws coding_exception
 * @throws dml_exception
 */
function cloudstudio_add_instance(stdClass $cloudstudio, $mform = null) {
    global $DB;

    $cloudstudio->timemodified = time();
    $cloudstudio->timecreated = time();
    $cloudstudio->livro = optional_param("livro", 1, PARAM_INT);
    $cloudstudio->mapamental = optional_param("mapamental", 1, PARAM_INT);

    $cloudstudio->id = $DB->insert_record('cloudstudio', $cloudstudio);

    \mod_cloudstudio\grade\grades_util::grade_item_update($cloudstudio);

    return $cloudstudio->id;
}

/**
 * function cloudstudio_update_instance
 *
 * @param stdClass $cloudstudio
 * @param mod_cloudstudio_mod_form|null $mform
 *
 * @return bool
 * @throws dml_exception
 * @throws coding_exception
 */
function cloudstudio_update_instance(stdClass $cloudstudio, $mform = null) {
    global $DB;

    $cloudstudio->timemodified = time();
    $cloudstudio->id = $cloudstudio->instance;
    $cloudstudio->livro = optional_param("livro", 1, PARAM_INT);
    $cloudstudio->mapamental = optional_param("mapamental", 1, PARAM_INT);

    $result = $DB->update_record('cloudstudio', $cloudstudio);

    \mod_cloudstudio\grade\grades_util::grade_item_update($cloudstudio);

    return $result;
}

/**
 * function cloudstudio_delete_instance
 *
 * @param int $id
 *
 * @return bool
 * @throws dml_exception
 * @throws coding_exception
 */
function cloudstudio_delete_instance($id) {
    global $DB;

    if (!$cloudstudio = $DB->get_record('cloudstudio', ['id' => $id])) {
        return false;
    }

    $fs = get_file_storage();
    $cm = get_coursemodule_from_id('cloudstudio', $cloudstudio->id);
    if ($cm) {
        $files = $fs->get_area_files(context_module::instance($cm->id)->id, 'mod_cloudstudio', 'content',
            $cloudstudio->id, 'sortorder DESC, id ASC', false);

        foreach ($files as $file) {
            $file->delete();
        }
    }
    $DB->delete_records('cloudstudio', ['id' => $cloudstudio->id]);
    $DB->delete_records('cloudstudio_view', ['cm_id' => $cm->id]);

    return true;
}

/**
 * function cloudstudio_user_outline
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $cloudstudio
 *
 * @return stdClass
 */
function cloudstudio_user_outline($course, $user, $mod, $cloudstudio) {
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * function cloudstudio_user_complete
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $cloudstudio
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function cloudstudio_user_complete($course, $user, $mod, $cloudstudio) {
    global $DB;

    $sql = "SELECT sv.user_id, sv.currenttime, sv.duration, sv.percent, sv.timecreated, sv.timemodified, sv.mapa,
                   u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.email
              FROM {cloudstudio_view} sv
              JOIN {user} u ON u.id = sv.user_id
             WHERE sv.cm_id   = :cm_id
               AND sv.user_id = :user_id
               AND percent    > 0
          ORDER BY sv.timecreated ASC";
    $param = [
        'cm_id' => $mod->id,
        'user_id' => $user->id,
    ];
    if ($registros = $DB->get_records_sql($sql, $param)) {
        echo "<table><tr>";
        echo "      <th>" . get_string('report_userid', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_nome', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_email', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_tempo', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_duracao', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_porcentagem', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_comecou', 'mod_cloudstudio') . "</th>";
        echo "      <th>" . get_string('report_terminou', 'mod_cloudstudio') . "</th>";
        echo "  </tr>";
        foreach ($registros as $registro) {
            echo "<tr>";
            echo "  <td>" . $registro->user_id . "</td>";
            echo "  <td>" . fullname($registro) . "</td>";
            echo "  <td>" . $registro->email . "</td>";
            echo "  <td>" . cloudstudio_format_time($registro->currenttime) . "</td>";
            echo "  <td>" . cloudstudio_format_time($registro->duration) . "</td>";
            echo "  <td>" . $registro->percent . "%</td>";
            echo "  <td>" . userdate($registro->timecreated) . "</td>";
            echo "  <td>" . userdate($registro->timemodified) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        print_string('no_data', 'cloudstudio');
    }
}

/**
 * Function cloudstudio_format_time
 *
 * @param $time
 *
 * @return string
 */
function cloudstudio_format_time($time) {
    if ($time < 60) {
        return "00:00:{$time}";
    } else {
        $horas = '';
        $minutos = floor($time / 60);
        $segundos = ($time % 60);

        if ($minutos > 59) {
            $horas = floor($minutos / 60);
            $minutos = ($minutos % 60);
        }

        $horas = substr("00{$horas}", -2);
        $minutos = substr("00{$minutos}", -2);
        $segundos = substr("00{$segundos}", -2);
        return "{$horas}:{$minutos}:{$segundos}";
    }
}

/**
 * function cloudstudio_get_coursemodule_info
 *
 * @param stdClass $coursemodule
 *
 * @return cached_cm_info
 * @throws dml_exception
 */
function cloudstudio_get_coursemodule_info($coursemodule) {
    global $DB, $CFG;

    $config = get_config('cloudstudio');

    $cloudstudio = $DB->get_record('cloudstudio', ['id' => $coursemodule->instance],
        'id, name, identificador, intro, introformat, completionpercent');

    $info = new cached_cm_info();
    if ($cloudstudio) {
        $info->name = $cloudstudio->name;
    }

    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('cloudstudio', $cloudstudio, $coursemodule->id, false);
    }

    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules']['completionpercent'] = $cloudstudio->completionpercent;
    }

    if ($config->display_popup) {
        $fullurl = "$CFG->wwwroot/mod/cloudstudio/view.php?id=$coursemodule->id&amp;mobile=1";
        $wh = "width=640,height=480,toolbar=no,location=no,menubar=no,copyhistory=no," .
            "status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('{$fullurl}', '', '{$wh}'); return false;";
    }

    return $info;
}

/**
 * Function cloudstudio_before_standard_html_head
 *
 * @throws dml_exception
 */
function cloudstudio_before_footer() {
    global $COURSE, $DB;

    if ($COURSE->id == 1 || $COURSE->format != "tiles" || AJAX_SCRIPT) {
        return;
    }

    $sql = "
        SELECT cm.id, cm.instance
          FROM {course_modules} cm
          JOIN {modules}         m ON cm.module = m.id
         WHERE cm.course = {$COURSE->id}
           AND m.name    = 'cloudstudio'";
    $modules = $DB->get_records_sql($sql);

    $css = "";
    foreach ($modules as $module) {
        $cloudstudio = $DB->get_record("cloudstudio", ["id" => $module->instance]);
        if ($cloudstudio) {
            $result = json_decode(cloudstudio_api::get("Arquivo/{$cloudstudio->identificador}/status"));
            if ($result) {
                $css .= "
                    .format-tiles-cm-list #module-{$module->id} {
                        background: url({$result->thumb});
                        background-size: contain;
                        background-position: top;
                    }
                    .format-tiles-cm-list #module-{$module->id} .tileiconcontainer {
                        background-color: transparent;
                    }";
            }
        }
    }
    echo "<style>{$css}</style>";
}


/**
 * Function cloudstudio_extend_settings_navigation
 *
 * @param settings_navigation $settings
 * @param navigation_node $cloudstudionode
 *
 * @throws coding_exception
 * @throws moodle_exception
 */
function cloudstudio_extend_settings_navigation($settings, $cloudstudionode) {
    global $PAGE;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $cloudstudionode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false && array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('moodle/course:manageactivities', $PAGE->cm->context)) {
        $node = navigation_node::create(get_string('report', 'mod_cloudstudio'),
            new moodle_url('/mod/cloudstudio/report.php', ['id' => $PAGE->cm->id]),
            navigation_node::TYPE_SETTING, null, 'mod_cloudstudio_report',
            new pix_icon('i/report', ''));
        $cloudstudionode->add_node($node, $beforekey);
    }
}


/**
 * Function cloudstudio_extend_navigation_course
 *
 * @param \navigation_node $navigation
 * @param stdClass $course
 * @param \context $context
 *
 * @throws coding_exception
 * @throws moodle_exception
 */
function cloudstudio_extend_navigation_course($navigation, $course, $context) {
    $node = $navigation->get('coursereports');
    if ($node && has_capability('mod/cloudstudio:view_report', $context)) {
        $url = new moodle_url('/mod/cloudstudio/reports.php', ['course' => $course->id]);
        $node->add(get_string('pluginname', 'cloudstudio'), $url, navigation_node::TYPE_SETTING, null, null,
            new pix_icon('i/report', ''));
    }
}

/**
 * Serve the files from the cloudstudio file areas
 *
 * @param stdClass $course    the course object
 * @param stdClass $cm        the course module object
 * @param context $context    the context
 * @param string $filearea    the name of the file area
 * @param array $args         extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options      additional options affecting the file serving
 *
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 * @throws coding_exception
 * @throws moodle_exception
 * @throws require_login_exception
 */
function cloudstudio_pluginfile($course, $cm, context $context, $filearea, $args, $forcedownload, array $options = []) {

    if ($filearea == "thumb") {
        $url = urldecode($args[0]);
        header("Location: {$url}");
        die();
    }

    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        $filepath = $args[0];
        $itemid = $args[1];
        $filename = $args[2];

        $fs = get_file_storage();

        $file = $fs->get_file($context->id, 'user', $filearea, $itemid, "/{$filepath}", $filename);
        if ($file) {
            send_stored_file($file, 86400, 0, $forcedownload, $options);
            return true;
        }
    }

    // Make sure the user is logged in and has access to the module
    // (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/cloudstudio:view', $context)) {
        return false;
    }

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        // Variable $args is empty => the path is '/'.
        $filepath = '/';
    } else {
        // Variable $args contains elements of the filepath.
        $filepath = '/' . implode('/', $args) . '/';
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_cloudstudio', $filearea, $itemid, $filepath, $filename);
    if ($file) {
        send_stored_file($file, 86400, 0, $forcedownload, $options);
        return true;
    }
    return false;
}

/**
 * Register the ability to handle drag and drop file uploads
 *
 * @return array containing details of the files / types the mod can handle
 * @throws coding_exception
 */
function cloudstudio_dndupload_register() {
    $ret = [
        'files' => [
            [
                'extension' => 'mp3',
                'message' => get_string('dnduploadlabel-mp3', 'mod_cloudstudio'),
            ],
            [
                'extension' => 'mp4',
                'message' => get_string('dnduploadlabel-mp4', 'mod_cloudstudio'),
            ],
            [
                'extension' => 'webm',
                'message' => get_string('dnduploadlabel-mp4', 'mod_cloudstudio'),
            ],
        ],
        'types' => [
            [
                'identifier' => 'text/html',
                'message' => get_string('dnduploadlabeltext', 'mod_cloudstudio'),
                'noname' => true,
            ],
            [
                'identifier' => 'text',
                'message' => get_string('dnduploadlabeltext', 'mod_cloudstudio'),
                'noname' => true,
            ],
        ],
    ];
    return $ret;
}

/**
 * Handle a file that has been uploaded
 *
 * @param stdClass $uploadinfo details of the file / content that has been uploaded
 *
 * @return int instance id of the newly created mod
 * @throws coding_exception
 * @throws dml_exception
 */
function cloudstudio_dndupload_handle($uploadinfo) {
    global $USER;

    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;

    $data->grade_approval = 0;

    $data->livro = 1;
    $data->mapamental = 1;

    $data->instance = cloudstudio_add_instance($data, null);

    $config = get_config('cloudstudio');
    if (isset($config->urlcloudstidio[10]) && isset($config->tokencloudstidio[10])) {

        $test = cloudstudio_dndupload_testupload('repo_upload_file');
        if (!$test['status']) {
            echo json_encode($test);
            die();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$config->urlcloudstidio}api/v1/Envio");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            'file' => '@' . realpath($_FILES['repo_upload_file']['tmp_name']),
            'titulo' => optional_param("title", "", PARAM_RAW),
            'descricao' => optional_param('descricao', "", PARAM_RAW),
            'identificador' => optional_param('identificador', "", PARAM_RAW),
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$config->tokencloudstidio}",
        ]);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $video = json_decode($result);

        if (isset($video->identificador)) {
            $data->identificador = $video->identificador;
            cloudstudio_update_instance($data, null);
        }

    } else {
        if (!empty($uploadinfo->draftitemid)) {
            $fs = get_file_storage();
            $draftcontext = context_user::instance($USER->id);
            $context = context_module::instance($uploadinfo->coursemodule);
            $files = $fs->get_area_files($draftcontext->id, 'user', 'draft', $uploadinfo->draftitemid, '', false);
            if ($file = reset($files)) {

                $data->identificador = "[resource-file:{$file->get_filename()}]";
                $options = ['subdirs' => true, 'embed' => true];
                file_save_draft_area_files($uploadinfo->draftitemid, $context->id,
                    'mod_cloudstudio', 'content', $data->instance, $options);

                cloudstudio_update_instance($data, null);
            }
        } else if (!empty($uploadinfo->content)) {
            $data->intro = $uploadinfo->content;
            if ($uploadinfo->type != 'text/html') {
                $data->introformat = FORMAT_PLAIN;
            }
        }
    }

    return $data->instance;
}

/**
 * Function cloudstudio_dndupload_testupload
 *
 * @param $file
 *
 * @return array
 */
function cloudstudio_dndupload_testupload($file) {
    if (!isset($_FILES[$file])) {
        return ['message' => "Nenhum upload recebido!", 'status' => false];
    }
    if ($_FILES[$file]['error'] > 0) {
        switch ($_FILES[$file]['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $erro = 'Erro 1: O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_FORM_SIZE:
                $erro = 'Erro 2: O arquivo excede o limite definido em MAX_FILE_SIZE no formulário.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_PARTIAL:
                $erro = 'Erro 3: O upload do arquivo foi feito parcialmente.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_NO_FILE:
                $erro = 'Erro 4: Nenhum arquivo foi enviado.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_NO_TMP_DIR:
                $erro = 'Erro 6: Pasta temporária ausênte.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_CANT_WRITE:
                $erro = 'Erro 7: Falha em escrever o arquivo no HD. Provavelmente o HD esteja lotado ou com falhas.';
                return ['message' => $erro, 'status' => false];
            case UPLOAD_ERR_EXTENSION:
                $erro = 'Erro 8: Uma extensão do PHP interrompeu o upload do arquivo. ".
                "O PHP não fornece uma maneira de determinar qual extensão causou a interrupção. ".
                "Examinar a lista das extensões carregadas com o phpinfo() pode ajudar.';
                return ['message' => $erro, 'status' => false];
            default:
                $erro = 'Erro de Upload!';
                return ['message' => $erro, 'status' => false];
        }
    }
    if (!file_exists($_FILES[$file]['tmp_name'])) {
        $erro = 'Arquivo não chegou!';
        return ['message' => $erro, 'status' => false];
    }

    return ['status' => true];
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 *
 * @return array $descriptions the array of descriptions for the custom rules.
 * @throws coding_exception
 */
function mod_cloudstudio_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules']) || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    $completionpercent = $cm->customdata['customcompletionrules']['completionpercent'] ?? 0;
    $descriptions[] = get_string("completionpercent_desc", "mod_cloudstudio", $completionpercent);
    return $descriptions;
}

/**
 * Sets the automatic completion state for this database item based on the
 * count of on its entries.
 *
 * @since Moodle 3.3
 *
 * @param object $data   The data object for this activity
 * @param object $course Course
 * @param object $cm     course-module
 *
 * @throws moodle_exception
 */
function cloudstudio_update_completion_state($data, $course, $cm) {

    // If completion option is enabled, evaluate it and return true/false.
    $completion = new completion_info($course);
    if ($data->completionpercent && $completion->is_enabled($cm)) {
        $numentries = data_numentries($data);
        // Check the number of entries required against the number of entries already made.
        if ($numentries >= $data->completionpercent) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        } else {
            $completion->update_state($cm, COMPLETION_INCOMPLETE);
        }
    }
}

/**
 * Obtains the automatic completion state for this database item based on any conditions
 * on its settings. The call for this is in completion lib where the modulename is appended
 * to the function name. This is why there are unused parameters.
 *
 * @deprecated since Moodle 3.11
 * @todo       MDL-71196 Final deprecation in Moodle 4.3
 * @see        \mod_data\completion\custom_completion
 * @since      Moodle 3.3
 *
 * @param stdClass $course     Course
 * @param cm_info|stdClass $cm course-module
 * @param int $userid          User ID
 * @param bool $type           Type of comparison (or/and; can be used as return value if no conditions)
 *
 * @return bool True if completed, false if not, $type if conditions not set.
 * @throws dml_exception
 */
function cloudstudio_get_completion_state($course, $cm, $userid, $type) {
    global $DB, $PAGE;

    // No need to call debugging here. Deprecation debugging notice already being called in \completion_info::internal_get_state().

    $result = $type; // Default return value
    // Get data details.
    if (isset($PAGE->cm->id) && $PAGE->cm->id == $cm->id) {
        $data = $PAGE->activityrecord;
    } else {
        $data = $DB->get_record('data', ['id' => $cm->instance], '*', MUST_EXIST);
    }
    // If completion option is enabled, evaluate it and return true/false.
    if ($data->completionpercent) {

        $numentries = 10;

        // Check the number of entries required against the number of entries already made.
        if ($numentries >= $data->completionpercent) {
            $result = true;
        } else {
            $result = false;
        }
    }
    return $result;
}
