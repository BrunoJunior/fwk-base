// Login OK --> Redirection
$('form').data('callback', function () {
    window.location.replace("/");
});

/**
 * Oublie de mot de passe
 */
$('#user_forgot').data('define-params', function(button, parameters) {
    parameters.email = $('#user_email').val();
});