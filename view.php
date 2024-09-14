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
 * view file
 *
 * @package   mod_cloudstudio
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_cloudstudio\analytics\cloudstudio_view;
use mod_cloudstudio\event\course_module_viewed;
use mod_cloudstudio\output\mobile;
use mod_cloudstudio\util\cloudstudio_api;

require_once('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);
$n = optional_param('n', 0, PARAM_INT);
$mobile = optional_param('mobile', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('cloudstudio', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $cloudstudio = $DB->get_record('cloudstudio', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($n) {
    $cloudstudio = $DB->get_record('cloudstudio', ['id' => $n], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cloudstudio->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('cloudstudio', $cloudstudio->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$secret = optional_param('secret', false, PARAM_TEXT);
if ($secret) {
    $userid = optional_param('user_id', "", PARAM_INT);
    mobile::valid_token($userid, $secret);
}

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/cloudstudio:view', $context);

$event = course_module_viewed::create([
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
]);
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $cloudstudio);
$event->trigger();

// Update 'viewed' state if required by completion system.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$params = [
    'n' => $n,
    'id' => $id,
    'mobile' => $mobile,
];
$PAGE->set_url('/mod/cloudstudio/view.php', $params);
$PAGE->requires->css('/mod/cloudstudio/style.css');
$PAGE->set_title("{$course->shortname}: {$cloudstudio->name}");
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

if ($mobile) {
    $PAGE->set_pagelayout('embedded');
}

echo $OUTPUT->header();

$isteacher = has_capability('moodle/course:manageactivities', $context);

$linkreport = "";
if ($isteacher) {
    $linkreport = "<a class='cloudstudio-report-link' href='report.php?id={$cm->id}'>" .
        get_string('report_title', 'mod_cloudstudio') . "</a>";
}
$title = format_string($cloudstudio->name);
echo $OUTPUT->heading("<span class='cloudstudioheading-title'>{$title}</span> {$linkreport}", 2, 'main', 'cloudstudioheading');

$config = get_config('cloudstudio');
$style = "";
if (@$config->maxwidth >= 500) {
    $config->maxwidth = intval($config->maxwidth);
    $style = "style='margin:0 auto;max-width:{$config->maxwidth}px;'";
}
echo "<div id='cloudstudio_area_embed' {$style}>";

$cloudstudioview = cloudstudio_view::create($cm->id);

$cloudstudio->identificador = cloudstudio_api::identificador($cloudstudio->identificador);

echo cloudstudio_api::get("Player/{$cloudstudio->identificador}/html", [
    'aluno_matricula' => $USER->id,
    'aluno_nome' => fullname($USER),
    'aluno_email' => $USER->email,
]);

$text = $OUTPUT->heading(get_string('view_seu_mapa', 'mod_cloudstudio') . ' <span></span>', 3, 'main-view', 'seu-mapa-view');
echo $OUTPUT->render_from_template('mod_cloudstudio/mapa', [
    'class' => $config->showmapa ? "" : "style='display:none'",
    'data-mapa' => base64_encode($cloudstudioview->mapa),
    'text' => $text,
]);

$tab = optional_param('tab', "livro", PARAM_TEXT);
$config = get_config('cloudstudio');
$url = "/mod/cloudstudio/view.php?id={$id}";
$tabs = [];
$tabcontent = "";
if ($cloudstudio->livro || $isteacher) {
    $tabs[] = new tabobject("livro", new moodle_url($url, ['tab' => 'livro']), get_string('view_livro', 'mod_cloudstudio'));
    if ($tab == "livro") {
        $json = cloudstudio_api::get("Ai/{$cloudstudio->identificador}/livro");
        $json = json_decode($json);

        if (isset($json->data) && isset($json->data->url)) {
            $tabcontent = "<iframe src='{$config->urlcloudstidio}vendor/pdfjs/web/viewer.html?file={$json->data->url}' 
                                   width='100%' height='800px' frameborder='0'></iframe>";
        } else {
            $tabcontent = get_string('view_ia_notfound', 'mod_cloudstudio');
        }
    }
}
if ($cloudstudio->mapamental || $isteacher) {
    $url = new moodle_url($url, ['tab' => 'mapamental']);
    $tabs[] = new tabobject("mapamental", $url, get_string('view_mapamental', 'mod_cloudstudio'));
    if ($tab == "mapamental") {
        $json = cloudstudio_api::get("Ai/{$cloudstudio->identificador}/mapamental");
        $json = json_decode($json);

        if (isset($json->data) && isset($json->data->mapamental)) {
            $mapaheight = $json->data->mapaheight * 1.3;
            $urlcloudstidio = $config->urlcloudstidio;
            $identificador = cloudstudio_api::identificador($cloudstudio->identificador);
            $tabcontent =
                "<iframe src='{$urlcloudstidio}Share/mapamental/{$identificador}?hidelink=1'
                         width='100%' height='800px' frameborder='0'
                         sandbox=\"allow-scripts allow-same-origin allow-popups\"
                         style=\"height:{$mapaheight}px\"></iframe>";
        } else {
            $tabcontent = get_string('view_ia_notfound', 'mod_cloudstudio');
        }
    }
}
if ($isteacher) {
    $url = new moodle_url($url, ['tab' => 'sugestao']);
    $tabs[] = new tabobject("sugestao", $url, get_string('view_sugestao', 'mod_cloudstudio'));
    if ($tab == "sugestao") {
        $json = cloudstudio_api::get("Ai/{$cloudstudio->identificador}/sugestao");
        $json = json_decode($json);

        if (isset($json->data) && isset($json->data[0])) {
            $tabcontent = $OUTPUT->render_from_template('mod_cloudstudio/tab/sugestao', $json);
        } else {
            $tabcontent = get_string('view_ia_notfound', 'mod_cloudstudio');
        }
    }

    $tabs[] = new tabobject("licao", new moodle_url($url, ['tab' => 'licao']), get_string('view_licao', 'mod_cloudstudio'));
    if ($tab == "licao") {
        $json = cloudstudio_api::get("Ai/{$cloudstudio->identificador}/licao");
        $json = json_decode($json);

        if (isset($json->data) && isset($json->data[0])) {
            $tabcontent = $OUTPUT->render_from_template('mod_cloudstudio/tab/licao', $json);
        } else {
            $tabcontent = get_string('view_ia_notfound', 'mod_cloudstudio');
        }
    }

    $tabs[] = new tabobject("short", new moodle_url($url, ['tab' => 'short']), get_string('view_short', 'mod_cloudstudio'));
    if ($tab == "short") {
        $json = cloudstudio_api::get("Ai/{$cloudstudio->identificador}/short");
        $json = json_decode($json);

        if (isset($json->data) && isset($json->data[0])) {
            $tabcontent = $OUTPUT->render_from_template('mod_cloudstudio/tab/short', $json);
        } else {
            $tabcontent = get_string('view_ia_notfound', 'mod_cloudstudio');
        }
    }
}

echo $OUTPUT->tabtree($tabs, $tab);
echo $tabcontent;

echo '</div>';

echo $OUTPUT->footer();
