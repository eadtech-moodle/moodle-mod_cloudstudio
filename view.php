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
 * @package    mod_cloudstudio
 * @copyright  2023 Eduardo kraus (http://eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);
$n = optional_param('n', 0, PARAM_INT);
$mobile = optional_param('mobile', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('cloudstudio', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $cloudstudio = $DB->get_record('cloudstudio', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $cloudstudio = $DB->get_record('cloudstudio', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cloudstudio->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('cloudstudio', $cloudstudio->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$secret = optional_param('secret', false, PARAM_TEXT);
if ($secret) {
    $userid = optional_param('user_id', "", PARAM_INT);
    \mod_cloudstudio\output\mobile::valid_token($userid, $secret);
}

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/cloudstudio:view', $context);

$event = \mod_cloudstudio\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $cloudstudio);
$event->trigger();

// Update 'viewed' state if required by completion system.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$params = [
    'n' => $n,
    'id' => $id,
    'mobile' => $mobile
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

$linkreport = "";
if (has_capability('moodle/course:manageactivities', $context)) {
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

$parseurl = \mod_cloudstudio\util\url::parse($cloudstudio->videourl);

$cloudstudioview = \mod_cloudstudio\analytics\cloudstudio_view::create($cm->id);

if ($parseurl->videoid) {
    $uniqueid = uniqid();

    $element_id = "{$parseurl->engine}-{$uniqueid}";

    if ($config->showcontrols == 2) {
        $cloudstudio->showcontrols = 0;
    } else if ($config->showcontrols == 3) {
        $cloudstudio->showcontrols = 1;
    }

    if ($config->autoplay == 2) {
        $cloudstudio->autoplay = 0;
    } else if ($config->autoplay == 3) {
        $cloudstudio->autoplay = 1;
    }

    if ($parseurl->engine == "cloudstudio") {
        die('Cloud Studio');
    }
    else if ($parseurl->engine == "resource") {
        $files = get_file_storage()->get_area_files(
            $context->id, 'mod_cloudstudio', 'content', $cloudstudio->id, 'sortorder DESC, id ASC', false);
        $file = reset($files);
        if ($file) {
            $path = "/{$context->id}/mod_cloudstudio/content/{$cloudstudio->id}{$file->get_filepath()}{$file->get_filename()}";
            $fullurl = moodle_url::make_file_url('/pluginfile.php', $path, false)->out();

            $embedparameters = implode(" ", [
                $cloudstudio->showcontrols ? "controls" : "",
                $cloudstudio->autoplay ? "autoplay" : "",
            ]);

            if ($parseurl->videoid == "mp3") {
                echo "<div id='{$element_id}'></div>";

                $PAGE->requires->js_call_amd('mod_cloudstudio/player_create', 'resource_audio', [
                    (int)$cloudstudioview->id,
                    $cloudstudioview->currenttime,
                    "{$parseurl->engine}-{$uniqueid}",
                    $fullurl,
                    $cloudstudio->autoplay ? true : false,
                    $cloudstudio->showcontrols ? true : false,
                ]);
            } else {
                echo "<div id='{$element_id}'></div>";

                $PAGE->requires->js_call_amd('mod_cloudstudio/player_create', 'resource_video', [
                    (int)$cloudstudioview->id,
                    $cloudstudioview->currenttime,
                    "{$parseurl->engine}-{$uniqueid}",
                    $fullurl,
                    $cloudstudio->autoplay ? true : false,
                    $cloudstudio->showcontrols ? true : false,
                ]);
            }
        } else {
            $message = "Arquivo não localizado!";
            $notification = new \core\output\notification($message, \core\output\notification::NOTIFY_ERROR);
            $notification->set_show_closebutton(false);
            echo \html_writer::span($PAGE->get_renderer('core')->render($notification));
        }
    }

    $text = $OUTPUT->heading(get_string('seu_mapa_view', 'mod_cloudstudio') . ' <span></span>', 3, 'main-view', 'seu-mapa-view');
    echo $OUTPUT->render_from_template('mod_cloudstudio/mapa', [
        'class' => $config->showmapa ? "" : "style='display:none'",
        'data-mapa' => base64_encode($cloudstudioview->mapa),
        'text' => $text
    ]);

} else {
    echo $OUTPUT->render_from_template('mod_cloudstudio/error');
    $config->showmapa = false;
}

echo '</div>';

echo $OUTPUT->footer();
