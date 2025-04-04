define(["jquery", "core/ajax", "core/notification", "core/templates"],
    function($, Ajax, Notification, Templates) {
        return {
            load__cloudstudio_files : function(path, page, titulo) {
                Ajax.call([{
                    methodname : "mod_cloudstudio_external_cloudstudio_files",
                    args       : {
                        path   : path,
                        page   : page,
                        titulo : titulo,
                    }
                }])[0].done(function(data) {
                    Templates.render("mod_cloudstudio/select-file-cloudstudio__galery", data)
                        .then(function(templatehtml) {
                            $("#galery-files").html(templatehtml);

                            $("#galery-files .galery-item-arquivo").click(function() {
                                var $this = $(this);

                                $("[name=name]").val($this.attr("data-titulo"));
                                $("[name=identificador]").val($this.attr("data-identificador"));

                                if (tinyMCE && tinyMCE.execCommand) {
                                    tinyMCE.execCommand("mceInsertContent", false, $this.attr("data-descricao"));
                                }

                                var event = new CustomEvent("cloudstudio-selected-file");
                                window.dispatchEvent(event);
                            });
                        });
                }).fail(Notification.exception);
            }
        }
    });