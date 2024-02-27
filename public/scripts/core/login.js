/**
 * Shutdown - Login
 * version 2.0
 * 
 *
 */
$(function () {

    // Toastr options
    // toastr.options = {
    //     "debug": false,
    //     "newestOnTop": false,
    //     "positionClass": "toast-top-center",
    //     "closeButton": true,
    //     "toastClass": "animated fadeInDown",
    // };

    $(".js-source-states").select2({
        minimumResultsForSearch: Infinity
    });

    $('.carousel').carousel({
        interval: 5000
      })

    $('#loginForm').submit(function () {
        event.preventDefault();
    });

    $("#password").keyup(function(event) {
        if (event.keyCode === 13) {
            $("#btnLogin").click();
        }
    });

    $('#sel-idioma').change(function () {
        var idioma = $(this).val();

        if (idioma != '-1' && idioma != -1) {
            CambiarIdioma(idioma);
        }
    });

    $('#btnLogin').click(function () {

        var btn_click_val   =   $('#btnLogin').text()
        $('#btnLogin').prop("disabled",true)

        $('#btnLogin').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')

        var UserLogin = {
            LoginName: $('#username').val(),
            Password: $('#password').val()
        };

        if (UserLogin) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/api/core/login',
                data: {
                    UserLogin: JSON.stringify(UserLogin)
                },
                success: function (data) {

                    if (!CheckSessionResult(data)) {
                        return;
                    }

                    if (data.ResultType == 'Action') {                        
                        if (data.Result.UrlRedirect != '') {
                            document.location.href = data.Result.UrlRedirect;
                        }
                    }
                    else if (data.ResultType == 'Error') {
                        $('#btnLogin').html(btn_click_val)
                        $("#btnLogin").prop("disabled",false)
                        Toast.fire({
                            icon: 'error',
                            title: data.ErrorMessage
                        });
                        //toastr.error(data.ErrorMessage);
                    }
                    else {
                        $('#btnLogin').html(btn_click_val)
                        $("#btnLogin").prop("disabled",false)
                        Toast.fire({
                            icon: 'error',
                            title: Localize("ErrorEstandar")
                        });
                        //toastr.error(Localize("ErrorEstandar"));
                    }
                },
                error: function (data) {
                    $('#btnLogin').html(btn_click_val)
                    $("#btnLogin").prop("disabled",false)
                    Toast.fire({
                        icon: 'error',
                        title: Localize("ErrorEstandar")
                    });
                    //toastr.error(Localize("ErrorEstandar"));
                }
            });
        }

    });

    $('#btnOlvidoClave').click(function () {
        $('#modalOlvidoClave').modal({ backdrop : 'static'});
    });

    $('#btn-enviar-email').click(function () {
        EnviarEmail($(this));
    });
});

function EnviarEmail(boton){
    
    //boton.prop('disabled', true);
    
    // Capturo el email
    var Email = $('#email-enviar').val().trim();

    // Valido el Email
    var validacion = ValidarEmail(Email);

    if (validacion != null) {

        Swal.fire({
            title: Localize("LblAtencion"),
            text: validacion,
            showCancelButton: false,
            icon: "error",
            confirmButtonText: "OK",
            closeOnConfirm: true
        });

        boton.prop('disabled', false);
        return;

    }
    else {
        // Enviar el email
        $.ajax({
            type: 'POST',
            url: '/api/core/resetearclave',
            contentType: false,
            processData: false,
            data: Email,
            success: function (data) {

                if (data.ErrorCode > 0) {
                    Toast.fire({
                        icon: 'error',
                        title: data.ErrorMessage
                    });
                    //toastr.error(data.ErrorMessage);
                    return;
                }
                else{
                    Swal.fire({
                        title: Localize("LblAtencion"),
                        text: Localize("LblEmailEnviado") + "\n" + "linkid=" + data.Data,
                        showCancelButton: false,
                        icon: "info",
                        confirmButtonText: "OK",
                        closeOnConfirm: true
                    });

                }

                $('#modalOlvidoClave').modal('hide');

            },
            error: function (data) {
                Toast.fire({
                    icon: 'error',
                    title: Localize("ErrorEstandar")
                });
                //toastr.error(Localize("ErrorEstandar"));
                $(boton).prop('disabled', false);
                HideLoader();
            }
        });
    }
}

function ValidarEmail(email) {

    var validarEmail = /^\w+([\.-]?\w)*@\w+([\.-]?\w+)*(\.\w{2,})+$/; 
    
    //Valida email si contiene "@" 
    //y que despues del punto final el dominio no contenga menos de 2 caracteres
    if(validarEmail.test(email) == false){
        return Localize("ErrorEmailUsuario");
    }
    
}
