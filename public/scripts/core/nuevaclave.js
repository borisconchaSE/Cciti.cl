$(function(){

    $('#btn_guardar').click( function() {

        GuardarCambioClave($(this));

    });

});


function GuardarCambioClave(boton) {

    // Lo primero que hago es deshabilitar el botón, para prevenir que el
    // usuario inexperto, haga DOBLE CLICK y llame 2 veces a la funcion
    boton.prop('disabled', true);

    // Capturar la informacion de la pantalla
    var clave = LeerClave();    

    // Validar la informacion
    var validacion = ValidarClave(clave);

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
        // Guardar el usuario
        $.ajax({
            type: 'POST',
            url: '/api/core/nuevopassword',
            contentType: false,
            processData: false,
            data: JSON.stringify(clave),
            success: function (data) {

                if (data.ErrorCode > 0) {
                    Toast.fire({
                        icon: 'error',
                        title: data.ErrorMessage
                    });
                    //toastr.error(data.ErrorMessage);
                    boton.prop('disabled', false); 
                    return;
                }
                Swal.fire({
                    title: Localize("LblAtencion"),
                    text: Localize("MsgEditarPassword"),
                    showCancelButton: false,
                    icon: "warning",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                }, function () {
                    location.href ="login";
                                
                });
                /* 
                boton.prop('disabled', false);
                toastr.info(Localize('MsgEditarPassword')); */
                

            },
            error: function (data) {
                Toast.fire({
                    icon: 'error',
                    title: Localize("ErrorEstandar")
                });
                //toastr.error(Localize("ErrorEstandar"));
                $(boton).prop('disabled', false);
            }
        });
    }

}

function LeerClave() {

    var Clave = {
        IdUsuario           : $('#id-usuario').val(),
        Password            : $('#clave-nueva').val(),
        RepitaPassword      : $('#repita-clave-nueva').val()
    };

    return Clave;

}

function ValidarClave(campos){
    var validarPassword = /((^[A-Z](?=.*[a-z]))|(^[a-z](?=.*[A-Z]))(?=.*\d)).{7,}$/;
    
    // ***La contraseña tiene que tener al menos 8 caracteres
    // ***Debe contener al menos 1 numero
    // ***Debe iniciar con una letra
    // ***Debe tener mayusculas y minusculas
    if(validarPassword.test(campos.Password) == false) {
        return Localize("ErrorPasswordUsuario");
    }
    else if (campos.Password != campos.RepitaPassword) {
        return Localize("ErrorPasswordNoCoincide");
    }
}
