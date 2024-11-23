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

define([
    "jquery", "core/ajax", "core/str", "core/modal_factory", "core/templates"
], function($, Ajax, getString, ModalFactory, Templates) {
    return mod_form = {
        id_name                : null,
        id_identificador       : null,
        fitem_id_identificador : null,

        init : function(courseSection) {

            mod_form.id_name = $("#id_name");
            mod_form.id_identificador = $("#id_identificador");

            mod_form.fitem_id_identificador = mod_form.find_fitem("identificador");

            console.log(courseSection);
            if (courseSection) {
                $("#id_generalcontainer").before(`
                        <div class="form-group row fitem has-danger">
                            <div class="col-md-3 col-form-label d-flex pb-0 pr-md-0">
                                <label class="d-inline word-break"></label>
                            </div>
                            <div class="col-md-9 form-inline align-items-start felement">
                                <div style="width:100%;">
                                    <span id="select-cloudstudio-open" class='btn btn-primary'>
                                        ${M.util.get_string('select_cloudstudio', 'cloudstudio')}   
                                    </span>
                                    <a id="kapture-open" class='btn btn-secondary' 
                                       href='${M.cfg.wwwroot}/mod/cloudstudio/vendor/kapture/?${courseSection}'>
                                        ${M.util.get_string('record_kapture', 'cloudstudio')}   
                                    </a>
                                </div>
                            </div>
                        </div>`);

                $("#select-cloudstudio-open").click(function() {
                    ModalFactory.create({
                        title : M.util.get_string('select_cloudstudio', 'cloudstudio'),
                        body  : Templates.render('mod_cloudstudio/select-file-cloudstudio'),
                    }).then(function(modal) {
                        modal.show();

                        window.addEventListener("cloudstudio-selected-file", function() {
                            modal.hide();
                        })
                    });
                });

                mod_form.id_name.focus(function() {
                    var videotitle = mod_form.id_name.val();
                    $("#kapture-open").attr("href", `${M.cfg.wwwroot}/mod/cloudstudio/vendor/kapture/?${courseSection}&videotitle=${videotitle}`)
                })
            }
        },

        find_fitem : function(fitem_id) {
            var key = "fitem_id_" + fitem_id;
            if (document.getElementById(key)) {
                return $("#" + key);
            }

            var element = $("#id_" + fitem_id);

            element = element.parent();
            if (element.hasClass("fitem")) {
                return element;
            }
            element = element.parent();
            if (element.hasClass("fitem")) {
                return element;
            }
            element = element.parent();
            if (element.hasClass("fitem")) {
                return element;
            }
            element = element.parent();
            if (element.hasClass("fitem")) {
                return element;
            }
            element = element.parent();
            if (element.hasClass("fitem")) {
                return element;
            }

            return $("#id_" + fitem_id).parent();
        }
    };
});
