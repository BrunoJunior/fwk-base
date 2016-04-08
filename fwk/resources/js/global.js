/**
 * Afficher un message d'erreur
 * @param {String} message
 * @param {JQuery Object} source
 * @returns {void}
 */
function afficherErr(message, source) {
    var div_alert_err = $('#cov-alert-error');
    if (source != undefined) {
        div_alert_err = source.closest('.container-alert').find('.cov-alert-error');
        if (div_alert_err.length < 1) {
            div_alert_err = $('#cov-alert-error');
        }
    }
    div_alert_err.find('.message').text(message);
    div_alert_err.removeClass('hidden');
    div_alert_err.fadeIn();
}

function cacherErr(source) {
    var div_alert_err = $('#cov-alert-error');
    if (source != undefined) {
        div_alert_err = source.closest('.container-alert').find('.cov-alert-error');
        if (div_alert_err.length < 1) {
            div_alert_err = $('#cov-alert-error');
        }
    }
    div_alert_err.fadeOut();
}

/**
 * Affichage message OK
 * @param {String} message
 * @param {JQuery Object} source
 * @returns {void}
 */
function afficherOK(message, source) {
    var div_alert_suc = $('#cov-alert-success');
    if (source != undefined) {
        div_alert_suc = source.closest('.container-alert').find('.cov-alert-success');
        if (div_alert_suc.length < 1) {
            div_alert_suc = $('#cov-alert-success');
        }
    }
    div_alert_suc.find('.message').text(message);
    div_alert_suc.removeClass('hidden');
    div_alert_suc.fadeIn();
}

function afficherLoading() {
    var div_loading = $('#loading');
    div_loading.fadeIn();
}

function cacherLoading() {
    $('#loading').fadeOut();
}

$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined && this.attr(name) !== false;
};


$(function () {
    var div_alert_err = $('#cov-alert-error');
    var div_alert_suc = $('#cov-alert-success');
    // Lors d'une déconnexion -> Redirection
    $('#cov-deco').data('callback', function (button, reponse) {
        window.location.replace("/");
    });

    // Gestion retour AJAX
    $('body').on('click', "[url*='.serv'],[href*='.serv']", function () {
        afficherLoading();
        var button = $(this);
        var url;
        if (button.hasAttr('url')) {
            url = button.attr('url');
        } else if (button.hasAttr('href')) {
            url = button.attr('href');
        }
        if (url === undefined) {
            afficherErr('Aucune url n\'est définie !');
            return;
        }
        // Si confirmation sur boutton
        var str_confirm = button.data('confirm');
        if (str_confirm !== undefined) {
            var r = confirm(str_confirm);
            if (r == false) {
                return;
            }
        }
        var callback = button.data('callback');
        var callback_always = button.data('callback-always');
        var define_params = button.data('define-params');
        var params = {};
        if (define_params !== undefined) {
            define_params(button, params);
        }
        div_alert_err.fadeOut();
        div_alert_suc.fadeOut();
        $.getJSON(url, params)
                .done(function (json) {
                    if (json.isErr) {
                        afficherErr(json.message, button);
                    } else if (callback !== undefined) {
                        callback(button, json.reponse);
                    }
                    if (!json.isErr) {
                        afficherOK(json.message, button);
                    }
                })
                .fail(function (jqxhr, textStatus, error) {
                    var err = textStatus + ", " + error;
                    afficherErr(err, button);
                })
                .always(function () {
                    cacherLoading();
                    if (callback_always !== undefined) {
                        callback_always(button);
                    }
                });
    });

    /**
     * Envoi d'un formulaire par AJAX
     */
    $('form button[type=submit]').on('click', function (e) {
        afficherLoading();
        var button = $(this);
        e.preventDefault();
        e.stopPropagation();
        var form = button.closest('form');
        var callback = form.data('callback');
        var callback_always = form.data('callback-always');
        div_alert_err.fadeOut();
        div_alert_suc.fadeOut();
        var data = form.serialize() + '&submit=' + button.attr('value');
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: data,
            dataType: 'json', // JSON
            success: function (json) {
                if (json.isErr) {
                    afficherErr(json.message, button);
                } else if (callback !== undefined) {
                    callback(json.reponse, form);
                }
                if (!json.isErr) {
                    afficherOK(json.message, button);
                }
            }
        }).fail(function (jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
            afficherErr(err, button);
        }).always(function () {
            cacherLoading();
            if (callback_always !== undefined) {
                callback_always(form);
            }
        });
        ;
        return false;
    });
});