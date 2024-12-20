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

define(["jquery", "core/ajax", "mod_cloudstudio/player_render"], function($, Ajax, PlayerRender) {
    return progress = {

        cloudstudio : function(view_id, return_currenttime, elementId, identificador) {
            window.addEventListener('message', function receiveMessage(event) {
                console.trace(event.data);

                if (event.data.origem == 'CLOUDSTUDIO-player' && event.data.name == "progress") {
                    progress._internal_saveprogress(event.data.currentTime, event.data.duration);
                }
            });
        },

        _internal_resize : function(width, height) {

            function _resizePage() {
                var videoBoxWidth = $("#cloudstudio_area_embed").width();
                var videoBoxHeight = videoBoxWidth * height / width;

                $("#cloudstudio_area_embed iframe").css({
                    //width  : videoBoxWidth,
                    height : videoBoxHeight,
                });
            }

            $(window).resize(_resizePage);
            _resizePage();

            var element = $("#cloudstudio_area_embed");
            var lastWidth = element.width();
            setInterval(function() {
                if (lastWidth === element.width()) return;
                lastWidth = element.width();

                _resizePage();
            }, 500);

            return element;

        },

        _internal_max_height : function() {
            $(window).resize(_resizePage);
            _resizePage();

            function _resizePage() {

                var $cloudstudioareaembed = $("#cloudstudio_area_embed");

                $cloudstudioareaembed.css({
                    "max-height" : "inherit",
                    "height"     : "inherit",
                });

                var header_height = ($("#header") && $("#header").height()) || 60;
                var window_height = $(window).height();

                var player_max_height = window_height - header_height;

                if ($cloudstudioareaembed.height() > player_max_height) {
                    $cloudstudioareaembed.css({
                        "max-height" : player_max_height,
                        "height"     : player_max_height
                    });
                }
            }
        },

        _internal_last_posicao_video : -1,
        _internal_last_percent       : -1,
        _internal_assistido          : [],
        _internal_view_id            : 0,
        _internal_progress_length    : 100,
        _internal_saveprogress       : function(currenttime, duration) {

            currenttime = Math.floor(currenttime);
            duration = Math.floor(duration);

            if (!duration) {
                return 0;
            }

            if (duration && progress._internal_assistido.length == 0) {
                progress._internal_progress_create(duration);
            }

            if (progress._internal_progress_length < 100) {
                posicao_video = currenttime;
            } else {
                var posicao_video = parseInt(currenttime / duration * progress._internal_progress_length);
            }

            if (progress._internal_last_posicao_video == posicao_video) return;
            progress._internal_last_posicao_video = posicao_video;

            if (posicao_video) {
                progress._internal_assistido[posicao_video] = 1;
            }

            var percent = 0;
            for (var j = 1; j <= progress._internal_progress_length; j++) {
                if (progress._internal_assistido[j]) {
                    percent++;
                    $("#mapa-visualizacao-" + j).css({opacity : 1});
                }
            }

            if (progress._internal_progress_length < 100) {
                percent = Math.floor(percent / progress._internal_progress_length * 100);
            }

            if (progress._internal_last_percent == percent) return;
            progress._internal_last_percent = percent;

            if (currenttime) {
                Ajax.call([{
                    methodname : 'mod_cloudstudio_external_progress_save',
                    args       : {
                        view_id     : progress._internal_view_id,
                        currenttime : parseInt(currenttime),
                        duration    : parseInt(duration),
                        percent     : parseInt(percent),
                        mapa        : JSON.stringify(progress._internal_assistido)
                    }
                }]);
            }

            if (percent >= 0) {
                $("#seu-mapa-view span").html(percent + "%");
            }
        },

        _internal_progress_create : function(duration) {

            var $mapa = $("#mapa-visualizacao .mapa");
            if (!$mapa.length) {
                return;
            }

            var cloudstudio_view_mapa = [];
            try {
                var mapa_json_base64 = $mapa.attr('data-mapa');
                if (mapa_json_base64) {
                    cloudstudio_view_mapa = JSON.parse(atob(mapa_json_base64));
                }
            } catch (e) {
                cloudstudio_view_mapa = [];
            }

            if (Math.floor(duration) <= 100) {
                progress._internal_progress_length = Math.floor(duration);
            }
            for (var i = 1; i <= progress._internal_progress_length; i++) {
                if (typeof cloudstudio_view_mapa[i] != "undefined") {
                    progress._internal_assistido[i] = cloudstudio_view_mapa[i];
                } else {
                    progress._internal_assistido[i] = 0;
                }
                var $mapaitem = $("<div id='mapa-visualizacao-" + i + "'>");
                $mapa.append($mapaitem);

                // Mapa Clique
                var mapaTitle = Math.floor(duration / progress._internal_progress_length * i);

                var hours = Math.floor(mapaTitle / 3600);
                var minutes = (Math.floor(mapaTitle / 60)) % 60;
                var seconds = mapaTitle % 60;

                var tempo = minutes + ":" + seconds;
                if (hours) {
                    tempo = hours + ":" + minutes + ":" + seconds;
                }
                var $mapaclique =
                        $("<div id='mapa-visualizacao-" + i + "'>")
                            .attr("title", 'Ir para ' + tempo)
                            .attr("data-currenttime", mapaTitle)
                            .click(function() {
                                var _setCurrentTime = $(this).attr("data-currenttime");
                                _setCurrentTime = parseInt(_setCurrentTime);

                                var event = document.createEvent('CustomEvent');
                                event.initCustomEvent('setCurrentTime', true, true, {goCurrentTime : _setCurrentTime});
                                document.dispatchEvent(event);
                            });
                $("#mapa-visualizacao .clique").append($mapaclique);
            }
        },

        _internal_add : function(accumulator, a) {
            return accumulator + a;
        }
    };
});
