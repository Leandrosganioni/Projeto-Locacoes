$(document).ready(function(){
    // Máscara para CPF (000.000.000-00)
    $('.cpf-mask').mask('000.000.000-00', {reverse: true});

    // Máscara para Celular/Telefone (com 8 ou 9 dígitos)
    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    options = {
        onKeyPress: function(val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };
    $('.phone-mask').mask(behavior, options);
});