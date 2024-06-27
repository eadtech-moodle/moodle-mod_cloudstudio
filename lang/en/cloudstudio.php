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
 * lang file
 *
 * @package    mod_cloudstudio
 * @copyright  2023 Eduardo kraus (http://eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Cloud Studio';
$string['pluginname'] = 'Cloud Studio';
$string['modulenameplural'] = 'Cloud Studio';

$string['dnduploadlabel-mp3'] = 'Add Audio with Cloud Studio';
$string['dnduploadlabel-mp4'] = 'Add Video with Cloud Studio';
$string['dnduploadlabeltext'] = 'Add Video with Cloud Studio';

$string['identificador'] = 'Cloud Studio Identifier';
$string['identificador_error'] = 'Cloud Studio Identifier Error';
$string['pluginadministration'] = 'Cloud Studio';
$string['modulename_help'] = 'This module adds Cloud Studio videos within Moodle.';
$string['loading'] = 'Loading';
$string['use_this_file'] = 'Use this';

$string['urlcloudstidio'] = 'Cloud Studio URL';
$string['urlcloudstidio_desc'] = 'Enter the Cloud Studio URL. This URL is required to connect your site to the Cloud Studio service and enable resource integration.';
$string['tokencloudstidio'] = 'Cloud Studio API TOKEN';
$string['tokencloudstidio_desc'] = 'Enter the API TOKEN provided by Cloud Studio. This token is used to authenticate and authorize communication between your site and Cloud Studio.';

$string['showmapa'] = 'Show Map';
$string['showmapa_desc'] = 'If checked, show the map after the video player!';
$string['maxwidth'] = 'Maximum Width for Video Player';
$string['maxwidth_desc'] = 'Maximum width, in pixels, that the video player can expand. Values less than 500 pixels will be considered.';
$string['record_kapture'] = 'Record your video with Kapture';
$string['select_cloudstudio'] = 'Select a video from Cloud Studio';

$string['livro'] = 'Show book, if available';
$string['livro_desc'] = 'Enable and allow the student to view the book, if available.';
$string['mapamental'] = 'Show mind map, if available';
$string['mapamental_desc'] = 'Enable and allow the student to view the mind map, if available.';


$string['view_seu_mapa'] = 'Your Visualization Map:';

$string['view_ia_notfound'] = 'Not Found';
$string['view_livro'] = 'Book';
$string['view_mapamental'] = 'Mind Map';
$string['view_sugestao'] = 'Suggestion of New Videos';
$string['view_licao'] = 'Suggestion of Lessons';
$string['view_short'] = 'Suggestion of Shorts';

$string['short_title'] = 'Short Suggestion based on Video Transcript';
$string['short_sugestao'] = 'Suggestion';
$string['short_start'] = 'Start at';
$string['short_at'] = 'up to the time';
$string['short_duration'] = 'with a duration of';
$string['sugestao_title'] = 'Extra Video Suggestions based on Transcript';

$string['report_title'] = 'Report';
$string['report'] = 'Visualization Report';
$string['report_userid'] = 'User ID';
$string['report_nome'] = 'Full Name';
$string['report_email'] = 'Email';
$string['report_tempo'] = 'Time Watched';
$string['report_duracao'] = 'Video Duration';
$string['report_porcentagem'] = 'Percentage Watched';
$string['report_mapa'] = 'Visualization Map';
$string['report_comecou'] = 'Started Watching When';
$string['report_terminou'] = 'Finished Watching When';
$string['report_visualizacoes'] = 'Views';
$string['report_assistiu'] = 'Watched When';
$string['report_all'] = 'All Views of This Student';
$string['report_filename'] = 'Cloud Studio Plugin Video Visualization - {$a}';
$string['report_filename_geral'] = 'General';

$string['grade_approval'] = 'Set Grade For';
$string['grade_approval_0'] = 'No Grades';
$string['grade_approval_1'] = 'Grade Based on Video Viewing Percentage';

$string['completionpercent'] = 'Required Percentage';
$string['completionpercent_help'] = 'Set as completed when the student views the defined percentage of the video. Accepted values are from 1 to 100.';
$string['completionpercent_error'] = 'Accepted values are from 1 to 100.';
$string['completionpercent_label'] = 'Enable:&nbsp;';
$string['completiondetail:completionpercent'] = 'Must watch {$a}% of the video';

$string['no_data'] = 'No Records';


$string['settings_opcional_desmarcado'] = 'In the FORM it will appear deactivated, and the teacher can activate or deactivate it';
$string['settings_opcional_marcado'] = 'In the FORM it will appear activated, and the teacher can activate or deactivate it';
$string['settings_obrigatorio_desmarcado'] = 'It will be deactivated for everyone and cannot be edited in the FORM';
$string['settings_obrigatorio_marcado'] = 'It will be activated for everyone and cannot be edited in the FORM';

$string['cloudstudio:addinstance'] = 'Create new activities with Video';
$string['cloudstudio:view'] = 'View and interact with the video';

$string['privacy:metadata'] = 'The Cloud Studio plugin does not send any personal data to third parties.';

$string['privacy:metadata:cloudstudio_view'] = 'A record of the messages sent during a chat session';

$string['privacy:metadata:cloudstudio_view:cm_id'] = 'Course Module ID';
$string['privacy:metadata:cloudstudio_view:user_id'] = 'User ID';
$string['privacy:metadata:cloudstudio_view:currenttime'] = 'Current Time';
$string['privacy:metadata:cloudstudio_view:duration'] = 'Duration';
$string['privacy:metadata:cloudstudio_view:percent'] = 'Percent';
$string['privacy:metadata:cloudstudio_view:mapa'] = 'Map';
$string['privacy:metadata:cloudstudio_view:timecreated'] = 'Time created';
$string['privacy:metadata:cloudstudio_view:timemodified'] = 'Time modified';

// Kapture.
$string['app_title'] = 'Kapture Cloud Studio Module';
$string['logo_title'] = 'Logo';
$string['selecionar_slide'] = 'Select Slide';
$string['layout_cam'] = 'Camera Layout';
$string['layout_presentation'] = 'Presentation Layout';
$string['layout_1'] = 'Layout 1';
$string['layout_2'] = 'Layout 2';
$string['layout_5'] = 'Layout 5';
$string['layout_4'] = 'Layout 4';
$string['layout_6'] = 'Layout 6';
$string['layout_3'] = 'Layout 3';
$string['finalizar_gravacao'] = 'End Recording';
$string['esta_aba'] = 'This Tab';
$string['tela_inteira'] = 'Full Screen';
$string['desligado'] = 'Off';
$string['inverter_camera'] = 'Invert Camera';
$string['camera_redonda'] = 'Round Camera';
$string['tamanho_camera'] = 'Camera Size';
$string['compartilhar_audio_sistema'] = 'Share System Audio';
$string['contagem_regressiva'] = 'Countdown';
$string['iniciar_gravacao'] = 'Start Recording';
$string['iniciar_gravacao_fullscreen'] = 'Start FullScreen Recording';
$string['kapture_precisa_camera'] = 'Kapture needs to access your microphone and camera.';
$string['aprovar_permissao'] = 'Select <b><i>Allow</i></b> when your browser requests permissions.';
$string['erro_camera_microfone.'] = 'An error occurred while requesting Camera and Microphone.';
$string['entre_contato_suporte_erro'] = 'Contact support and report the error ';
$string['camera'] = 'Camera';
$string['microfone'] = 'Microphone';
$string['erro'] = 'Error:';
$string['nao_suportado_celular'] = 'Not supported on Mobile';
$string['finalizar'] = 'End';
$string['salvar_gravacao_ottflix'] = 'Save your recording to Cloud Studio Module';
$string['title_captura'] = 'Capture';
$string['salvar_ottflix'] = 'Save to Cloud Studio Module';
$string['salvar_computador'] = 'Save to Computer';
$string['selecione_apresentacao'] = 'Select Presentation';
$string['buscar_arquivos'] = 'Search Files';
$string['enviar_novo'] = 'Upload New';
$string['ou'] = 'or';
$string['carregando_documentos'] = 'Loading documents...';
$string['processando'] = 'Processing...';
$string['titulo_muito_curto'] = 'Title too short!';
$string['upload_concluido'] = 'Upload complete. Awaiting processing!';
$string['ottflix'] = 'Cloud Studio Module';
$string['falha_upload_ottflix'] = 'Upload to Cloud Studio Module Failed';
$string['abortado_upload_ottflix'] = 'Upload to Cloud Studio Module was aborted!';
$string['error_accessing_camera'] = 'Error accessing camera or capturing screen';
