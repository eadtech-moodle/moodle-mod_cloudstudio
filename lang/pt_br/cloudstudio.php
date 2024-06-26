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

$string['dnduploadlabel-mp3'] = 'Adicionar Áudio com o Cloud Studio';
$string['dnduploadlabel-mp4'] = 'Adicionar Vídeo com o Cloud Studio';
$string['dnduploadlabeltext'] = 'Adicionar Vídeo com o Cloud Studio';

$string['identificador'] = 'Identificador do Cloud Studio';
$string['identificador_error'] = 'Identificador do Cloud Studio';
$string['pluginadministration'] = 'Cloud Studio';
$string['modulename_help'] = 'Este módulo adiciona um Vídeos do Cloud Studio dentro do Moodle.';
$string['loading'] = 'Carregando';
$string['use_this_file'] = 'Usar este';

$string['urlcloudstidio'] = 'URL do Cloud Studio';
$string['urlcloudstidio_desc'] = 'Insira a URL do Cloud Studio. Esta URL é necessária para conectar seu site ao serviço de Cloud Studio e possibilitar a integração dos recursos.';
$string['tokencloudstidio'] = 'TOKEN da API do Cloud Studio';
$string['tokencloudstidio_desc'] = 'Insira o TOKEN da API fornecido pelo Cloud Studio. Este token é utilizado para autenticar e autorizar a comunicação entre seu site e o Cloud Studio.';

$string['showmapa'] = 'Mostrar Mapa';
$string['showmapa_desc'] = 'Se marcado, mostra o mapa após o player do vídeo!';
$string['maxwidth'] = 'Largura Máxima para player de Vídeo';
$string['maxwidth_desc'] = 'Largura máxima, em pixels, que o player de vídeo pode expandir. Valores inferiores a 500 pixels serão considerados.';
$string['record_kapture'] = 'Grave seu vídeo com Kapture';
$string['select_cloudstudio'] = 'Selecione um vídeo do Cloud Studio';

$string['livro'] = 'Mostrar livro, caso houver';
$string['livro_desc'] = 'Habilite e permita ao aluno visualizar o livro, se disponível.';
$string['mapamental'] = 'Mostrar Mapa mental, caso houver';
$string['mapamental_desc'] = 'Habilite e permita ao aluno visualizar o mapa mental, se disponível.';

$string['short_title'] = 'Sugestão de Short baseado na transcrição do vídeo';
$string['short_sugestao'] = 'Sugestão';
$string['short_start'] = 'Início em';
$string['short_at'] = 'até o tempo';
$string['short_duration'] = 'com duração de';
$string['sugestao_title'] = 'Sugestões de Vídeos Extras com base na transcrição';


$string['view_seu_mapa'] = 'Seu mapa de Visualização:';

$string['view_ia_notfound'] = 'Não localizado';
$string['view_livro'] = 'Livro';
$string['view_mapamental'] = 'Mapa Mental';
$string['view_sugestao'] = 'Sugestão de novos vídeos';
$string['view_licao'] = 'Sugestão de Lições';
$string['view_short'] = 'Sugestão de Shorts';

$string['report_title'] = 'Relatório';
$string['report'] = 'Relatório de visualizações';
$string['report_userid'] = 'User ID';
$string['report_nome'] = 'Nome Completo';
$string['report_email'] = 'E-mail';
$string['report_tempo'] = 'Tempo assistido';
$string['report_duracao'] = 'Duração do Vídeo';
$string['report_porcentagem'] = 'Porcentagem visto';
$string['report_mapa'] = 'Mapa da Visualização';
$string['report_comecou'] = 'Começou a assistir quando';
$string['report_terminou'] = 'Terminou de assistir quando';
$string['report_visualizacoes'] = 'Visualizações';
$string['report_assistiu'] = 'Assistiu quando';
$string['report_all'] = 'Todos as visualizações deste aluno';
$string['report_filename'] = 'Visualização de vídeos do Plugin Cloud Studio - {$a}';
$string['report_filename_geral'] = 'Geral';

$string['grade_approval'] = 'Definir nota para';
$string['grade_approval_0'] = 'Sem notas';
$string['grade_approval_1'] = 'Nota baseado na porcentagem da visuaização do vídeo';

$string['completionpercent'] = 'Requer porcentagem';
$string['completionpercent_help'] = 'Definir como concluído quando o aluno visualizar a porcentagem do vídeo definida. Aceito valores de de 1 à 100';
$string['completionpercent_error'] = 'Aceito valores de de 1 à 100';
$string['completionpercent_label'] = 'Habilitar:&nbsp;';
$string['completiondetail:completionpercent'] = 'Tem que assistir {$a}% do vídeo';

$string['no_data'] = 'Sem registros';

$string['settings_opcional_desmarcado'] = 'No FORM aparecerá desativado e o professor poderá ativar ou desativar';
$string['settings_opcional_marcado'] = 'No FORM aparecerá ativado e o professor poderá ativar ou desativar';
$string['settings_obrigatorio_desmarcado'] = 'Será desativado para todos e não há como editar no FORM';
$string['settings_obrigatorio_marcado'] = 'Será ativado para todos e não há como editar no FORM';

$string['cloudstudio:addinstance'] = 'Crie novas atividades com Vídeo';
$string['cloudstudio:view'] = 'Ver e interagir com o vídeo';

$string['privacy:metadata'] = 'O plug-in Cloud Studio não envia nenhum dado pessoal a terceiros.';

$string['privacy:metadata:cloudstudio_view'] = 'A record of the messages sent during a chat session';
$string['privacy:metadata:cloudstudio_view:cm_id'] = '';
$string['privacy:metadata:cloudstudio_view:user_id'] = '';
$string['privacy:metadata:cloudstudio_view:currenttime'] = '';
$string['privacy:metadata:cloudstudio_view:duration'] = '';
$string['privacy:metadata:cloudstudio_view:percent'] = '';
$string['privacy:metadata:cloudstudio_view:mapa'] = '';
$string['privacy:metadata:cloudstudio_view:timecreated'] = '';
$string['privacy:metadata:cloudstudio_view:timemodified'] = '';

// Kapture.
$string['app_title'] = 'Módulo Cloud Studio Kapture';
$string['logo_title'] = 'Logo';
$string['selecionar_slide'] = 'Selecionar Slide';
$string['layout_cam'] = 'Layout Cam';
$string['layout_presentation'] = 'Layout Presentation';
$string['layout_1'] = 'Layout 1';
$string['layout_2'] = 'Layout 2';
$string['layout_5'] = 'Layout 5';
$string['layout_4'] = 'Layout 4';
$string['layout_6'] = 'Layout 6';
$string['layout_3'] = 'Layout 3';
$string['finalizar_gravacao'] = 'Finalizar a gravação';
$string['esta_aba'] = 'Esta aba';
$string['tela_inteira'] = 'Tela Inteira';
$string['desligado'] = 'desligado';
$string['inverter_camera'] = 'Inverter Câmera';
$string['camera_redonda'] = 'Câmera Redonda';
$string['tamanho_camera'] = 'Tamanho da câmera';
$string['compartilhar_audio_sistema'] = 'Compartilhar áudio do sistema';
$string['contagem_regressiva'] = 'Contagem regressiva';
$string['iniciar_gravacao'] = 'Iniciar gravação';
$string['iniciar_gravacao_fullscreen'] = 'Iniciar gravação em FullScreen';
$string['kapture_precisa_camera'] = 'O Kapture precisa acessar seu microfone e câmera.';
$string['aprovar_permissao'] = 'Selecione <b><i>Permitir</i></b> quando seu navegador solicitar permissões.';
$string['erro_camera_microfone.'] = 'Ocorreu um erro ao solicitar a Câmera e Microfone.';
$string['entre_contato_suporte_erro'] = 'Entre em contato com suporte e informe o erro ';
$string['camera'] = 'Câmera';
$string['microfone'] = 'Microfone';
$string['erro'] = 'Erro:';
$string['nao_suportado_celular'] = 'Não suportado em Celular';
$string['finalizar'] = 'Finalizar';
$string['salvar_gravacao_ottflix'] = 'Salve sua gravação no Módulo Cloud Studio';
$string['title_captura'] = 'Captura ';
$string['salvar_ottflix'] = 'Salvar no Módulo Cloud Studio';
$string['salvar_computador'] = 'Salvar no Computador';
$string['selecione_apresentacao'] = 'Selecione a apresentação';
$string['buscar_arquivos'] = 'Buscar arquivos';
$string['enviar_novo'] = 'Enviar novo';
$string['ou'] = 'ou';
$string['carregando_documentos'] = 'Carregando documentos...';
$string['processando'] = 'Processando...';
$string['titulo_muito_curto'] = 'Título muito curto!';
$string['upload_concluido'] = 'Upload concluído. Aguardando processamento!';
$string['ottflix'] = 'Módulo Cloud Studio';
$string['falha_upload_ottflix'] = 'Falha no Upload para o Módulo Cloud Studio';
$string['abortado_upload_ottflix'] = 'Upload para o Módulo Cloud Studio foi abortado!';
$string['error_accessing_camera'] = 'Error accessing camera or capturing screen';



