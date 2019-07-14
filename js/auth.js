//$('.pay-content label').click(function() {
//    var sum = $(this).attr('data-target');
//    $(this).closest(".card-body").find("span").html(sum);
//});

$(document).ready(function() {
    $.ajax({
        url: "http://projectcenter.ddns.net/api.php",
        cache: false,
        type: "GET",
        dataType: 'json',
        success: function(vars) {
            if (vars['auth']['status'] == 'ok')
                location="/";
        },
        error: function() {
            alert("Error");
        }
    });
});

$('button').click(function(){
    var mode = $(this).attr('id');
    
    if (mode == 'register') {
        $('.register input').removeClass('is-invalid');
        
        var login = $('#Login').val();
        if (!login)
            validater(".register .login");
        var pass = $('#Password1').val();
        var pass_confirm = $('#Password_confirm').val();
        if (!pass_confirm || !pass)
            validater(".register .pass");
        var passport_serial = $('#Passport_serial').val();
        var passport_number = $('#Passport_number').val();
        if (!passport_number || !passport_serial)
            validater(".register .passport");
        var first_name = $('#First_name').val();
        var last_name = $('#Last_name').val();
        var second_name = $('#Second_name').val();
        if (!second_name || !last_name || !second_name)
            validater(".register .fio");
        
        if (pass != pass_confirm) {
            $('.register .pass input').addClass('is-invalid');
        } else {
            var params = "module=register&login=" + login + "&pass=" + pass + "&series_passport=" + passport_serial + "&number_passport=" + passport_number + "&first_name=" + first_name + "&last_name=" + last_name + "&second_name=" + second_name;
            
            $.ajax({
                url: "http://projectcenter.ddns.net/api.php",
                cache: false,
                type: "GET",
                dataType: 'json',
                data: params,
                success: function(vars) {
                    if (vars['status'] == 'error') {
                        $('.register .' + vars['error'] + " .invalid-feedback").text(vars['error_msg']);
                        $('.register .' + vars['error'] + ' input').addClass('is-invalid');
                    } else {
                        location="/";
                    }
                },
                error: function() {
                    alert("Error");
                }
            });
        }
    } else if (mode == 'login') {
        $('.login_form input').removeClass('is-invalid');
        
        var login = $('#Login_login').val();
        if (!login)
            validater('.login_form .login');
        var pass = $('#Password_login').val();
        if (!pass)
            validater('.login_form .pass');
        
        var params = "module=auth&login=" + login + "&pass=" + pass;
            
        $.ajax({
            url: "http://projectcenter.ddns.net/api.php",
            cache: false,
            type: "GET",
            dataType: 'json',
            data: params,
            success: function(vars) {
                if (vars['status'] == 'error') {
                    $('.login_form input').addClass('is-invalid');
                } else {
                    location="/";
                }
            },
            error: function() {
                alert("Error");
            }
        });
    }
});

$('input').click(function(){
    $(this).removeClass('is-invalid');
});

function validater(selector) {
    $(selector + " .invalid-feedback").text("Заполните поле!");
    $(selector + " input").addClass("is-invalid");
}