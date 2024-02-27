function btnEditarUsuario_OnClick(eventInfo){ 

 
    const popupChangePassword = NewPopUp({
        dismissOnOutsideClick : true
    });
 

    var idUsuario   =   eventInfo.RowData.idusuario
 
    var entity      =   {
        "IdUsuario" :   idUsuario
    } 

    const service = new AdminUsuarioSvc(); 
 
    service.PopupEditarUsuario(
        entity,
        function(result){
            $(popupChangePassword).RefreshPopUp(result);
        },
        function(errorCode,errorMessage){ 
            $('.modal').modal('hide'); 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )

}



function btnGuardarCambiosUsuario_OnClick(eventInfo){

    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnGuardarCambiosUsuario').html(); 
    $('#btnGuardarCambiosUsuario').addClass('disabled')

    $('#btnGuardarCambiosUsuario').html('<i class="fa fa-spinner fa-pulse"></i>');
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    
    var status =    validarInputsUsuario(frmkey,false)
    if (status != true){
        $('#btnGuardarCambiosUsuario').removeClass('disabled')
        $('#btnGuardarCambiosUsuario').html(txtbackup); 
        return; 
    }



    var IdUsuario       =   eventInfo.FormData.IdUsuario;
    var Nombre          =   eventInfo.FormData.Nombre;
    var Sigla           =   eventInfo.FormData.Sigla;
    var Cargo           =   eventInfo.FormData.Cargo;

    var IdTipoUsuario   =   eventInfo.FormData.IdTipoUsuario;
    var IdJefeDirecto   =   eventInfo.FormData.IdJefeDirecto;
     
    var entity      =   {
        "DatosUsuario"  :   JSON.stringify({
            IdUsuario       :   IdUsuario,
            Nombre          :   Nombre,
            Sigla           :   Sigla,
            Cargo           :   Cargo,
            IdTipoUsuario   :   IdTipoUsuario,
            IdJefeDirecto   :   IdJefeDirecto
        })
    }

    const service = new AdminUsuarioSvc(); 
 
    service.CambiarParametrosUsuario(
        entity,
        function(result){  
            $('.modal').modal('hide');  
            $(`td[data-pk="${IdUsuario}"][data-property-name="Nombre"]`).html(Nombre)
            $(`td[data-pk="${IdUsuario}"][data-property-name="Sigla"]`).html( `<div class="profile-circle default">${Sigla}</div>`)
            $(`td[data-pk="${IdUsuario}"][data-property-name="Cargo"]`).html(Cargo)
            Toast.fire({
                icon: 'success',
                title: 'Cambios guardados correctamente'
            }); 
        },
        function(errorCode,errorMessage){ 
            $('#btnGuardarCambiosUsuario').removeClass('disabled')
            $('#btnGuardarCambiosUsuario').html(txtbackup); 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )
}


function btnDesactivarUsuario_OnClick(eventInfo) {  



 

    /* obtenemos el id del usuario */
    var idUsuario           =   eventInfo.RowData.idusuario;
    var table_element       =   $(`td[data-pk=${idUsuario}][data-property-name="Eliminado"]`);

    var TipoAccion          =   table_element.text().trim() == 'Activo' ? 'desactivar' : 'activar'


    /* levantamos una alaert */
    Swal.fire(
        {
            icon                : 'warning',
            title               : `¿Esta seguro/a que desea ${TipoAccion} el usuario?`,
            showCancelButton    : true,
            confirmButtonText   : TipoAccion.toUpperCase(),
            reverseButtons      : true,
            cancelButtonText    : `CANCELAR`
        }
    ).then((result) => 
        {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) { 

                Swal.fire({
                    title: "Procesando solicitud", 
                    timerProgressBar: false,
                    didOpen: () => {
                      Swal.showLoading(); 
                    },
                 
                });

                const service = new AdminUsuarioSvc(); 

                var entity      =   {
                    "IdUsuario" : idUsuario
                }
                service.ActivarDesactivarUsuario(
                    entity,
                    function(result){    

                        var table_element       =   $(`td[data-pk=${result.IdUsuario}][data-property-name="Eliminado"]`);
                        var btn_element         =   $( $(`tr[data-pk=${result.IdUsuario}]`).children()[7].children[0].children[2] )

           
                        if (result.Estado == 1) {
                            $(table_element).html(`<center> <span class="badge badge-soft-danger">Desactivado</span> </center>`)

                            $(btn_element).removeClass('btn-success')
                            $(btn_element).removeClass('btn-danger')
                            $(btn_element).attr('data-bs-original-title','Activar Usuario')
                            $(btn_element).addClass('btn-success') 
                        }else{

                            $(table_element).html(`<center> <span class="badge badge-soft-success">Activado</span> </center>`)

                            $(btn_element).removeClass('btn-success')
                            $(btn_element).removeClass('btn-danger')
                            $(btn_element).attr('data-bs-original-title','Desactivar Usuario')
                            $(btn_element).addClass('btn-danger') 
                        }
                       
                        console.log('estado cambiado');
                        console.log(result);
                        /* eliminamos la clase y el texto del botón */
                        Toast.fire({
                            icon: 'success',
                            title: 'Solicitud procesada corretamente'
                        });

                        return true




                    },
                    function(errorCode,errorMessage){  
                        Toast.fire({
                            icon: 'error',
                            title: 'Usuario invalido o usted no tiene acceso a este recurso'
                        });
                    }
                )

                
            } 
        }
    );

    return
 


}
 



function btnNuevoUsuario_OnClick(){

    const popoUpNuevoUsuario = NewPopUp({
        dismissOnOutsideClick : true
    }); 
 

    const service = new AdminUsuarioSvc(); 
 
    service.PopupNuevoUsuario(
        null,
        function(result){
            $(popoUpNuevoUsuario).RefreshPopUp(result);
        },
        function(errorCode,errorMessage){ 
            $('.modal').modal('hide'); 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )

}


function btnCrearNuevoUsuario_OnClick(eventInfo){
    console.log(eventInfo);

    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnCrearNuevoUsuario').html(); 
    
    $('#btnCrearNuevoUsuario').addClass('disabled') 
    $('#btnCrearNuevoUsuario').html('<i class="fa fa-spinner fa-pulse"></i>');

    
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var status =    validarInputsUsuario(frmkey)
    if (status != true){
        $('#btnCrearNuevoUsuario').html(txtbackup); 
        $('#btnCrearNuevoUsuario').removeClass('disabled') 
        return; 
    } 
     
    /* ------------------------------------------------------------------------------------------------ */
    console.log('todo ok')

    var entity      =   {
        "NuevoUsuario"  :  JSON.stringify({
            "Cargo"         :   validarTextoInput(eventInfo.FormData.Cargo),
            "IdTipoUsuario" :   eventInfo.FormData.IdTipoUsuario,
            "IdUsuario"     :   eventInfo.FormData.IdUsuario,
            "LoginName"     :   eventInfo.FormData.LoginName,
            "Nombre"        :   validarTextoInput(eventInfo.FormData.Nombre),
            "Sigla"         :   validarTextoInput(eventInfo.FormData.Sigla),
            "Password"      :   eventInfo.FormData.Password
        })
    } 

    const service = new AdminUsuarioSvc(); 
 
    service.GuardarNuevoUsuario(
        entity,
        function(result){
            console.log(result);
            DibujarNuevoContacto(result);
            $('.modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Usuario creado correctamente'
            });
        },
        function(errorCode,errorMessage){ 
            $('#btnCrearNuevoUsuario').html(txtbackup); 
            $('#btnCrearNuevoUsuario').removeClass('disabled') 
            var statusInputCorreo = ValidarInput( /* Invocamos la función que permite validar los inputs */
                `${frmkey}-LoginName`, /* facilitamos el ID del input que debemos validar */
                function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
                    return false;
                }, /*  --- */
                '', /* Ingresamos el mensaje que se debe mostrar en caso de ser valido */
                'El correo ya esta registrado' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
            )
         
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )


}

/* -------------------------------------------------------------------------------------------------- */
/* ESTA FUNCIÓN NOS PERMITE COMPATIBILIZAR LOS DATOS GENERADOS DESDE EL FRONT VS LOS DATOS DEL BACK
/* -------------------------------------------------------------------------------------------------- */
function DibujarNuevoContacto(DatosUsuario = {
    'IdUsuario' :   999,
    'Nombre'    :   'Prueba',
    'Cargo'     :   'Prueba',
    'LoginName' :   'Prueba@test.cl',
    'Sigla'     :   'PT',
}, Permisos = []){
 
    var table       = $('#tbListadoUsuarios').DataTable();

    var idCell      =   DatosUsuario.IdUsuario;
    var Nombre      =   DatosUsuario.Nombre;
    var Cargo       =   DatosUsuario.Cargo;
    var Correo      =   DatosUsuario.LoginName;
    var Estado      =   '<center> <span class="badge badge-soft-success">Activo</span> </center>';
    var TipoClave   =   '<center> <span class="badge badge-soft-info">Definitiva</span> </center>';
    var Sigla       =   `<div class="center"><div class="profile-circle">${DatosUsuario.Sigla}</div></div>`;

    var DisabledPasswordBtn     =   $('#btnGenerarContraseña').hasClass('disabled')     ? 'disabled' : '';
    var DisabledEditUser        =   $('#btnEditarUsuario').hasClass('disabled')         ? 'disabled' : '';
    var DisabledActiveUser      =   $('#btnDesactivarUsuario').hasClass('disabled')     ? 'disabled' : '';

    var buttons     =   `<div class="text-right one-line-text">
                <a role="button" class="btn-sm ${DisabledPasswordBtn} btnGenerarContraseña btn btn-primary" style="position: relative;  cursor: pointer; " id="btnGenerarContraseña" name="btnGenerarContraseña" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Desactivar Usuario" data-pk="1">
                <i class="fa fa-reply"></i>
                <div class="hide badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="btnGenerarContraseñaBadge">
                <span class="badge bg-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="btnGenerarContraseñaBadgeContent">
                
            </span>
            </div>
            </a>
            <a role="button" onclick="__tceu(this)" class="btn-sm ${DisabledEditUser} btnEditarUsuario btn btn-warning" style="position: relative;  cursor: pointer; " disabled="" id="btnEditarUsuario" name="btnEditarUsuario" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Editar Usuario" data-pk="1">
                <i class="fa fa-edit"></i>
                <div class="hide badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="btnEditarUsuarioBadge">
                <span class="badge bg-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="btnEditarUsuarioBadgeContent">
                
            </span>
            </div>
            </a>
            <a role="button" onclick="__tcd(this)" class="btn-sm ${DisabledActiveUser} btnDesactivarUsuario btn btn-danger" style="position: relative;  cursor: pointer; " disabled="" id="btnDesactivarUsuario" name="btnDesactivarUsuario" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Desactivar Usuario" data-pk="1">
                <i class="fa fa-power-off"></i>
                <div class="hide badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="btnDesactivarUsuarioBadge">
                <span class="badge bg-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="btnDesactivarUsuarioBadgeContent">
                
            </span>
            </div>
            </a>
            </div>`;
    


    var rowNode     = table.row.add( [ idCell, Nombre, Cargo,Correo,Estado,TipoClave,Sigla,buttons ] ).draw().node();
    
    /* AGREGAMOS LOS VALORES PK */
    debugger;
    $(rowNode).attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).attr('data-idusuario',DatosUsuario.IdUsuario) 

    /* AGREGAMOS LOS IDS QUE CORRESPONDEN */
    $(rowNode).find('td:eq(0)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(1)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(2)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(3)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(4)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(5)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(6)').attr('data-pk',DatosUsuario.IdUsuario)
    $(rowNode).find('td:eq(7)').attr('data-pk',DatosUsuario.IdUsuario)

    /* INCORPORAMOS EL NOMBRE DE LA PROPIEDAD A LA TABLA */
    $(rowNode).find('td:eq(0)').attr('data-property-name','IdUsuario')
    $(rowNode).find('td:eq(1)').attr('data-property-name','Nombre')
    $(rowNode).find('td:eq(2)').attr('data-property-name','Cargo')
    $(rowNode).find('td:eq(3)').attr('data-property-name','LoginName')
    $(rowNode).find('td:eq(4)').attr('data-property-name','Eliminado')
    $(rowNode).find('td:eq(5)').attr('data-property-name','IdTipoClave')
    $(rowNode).find('td:eq(6)').attr('data-property-name','Sigla') 

}




function __tceu(btn){

    var me = $(btn)[0];
    var row = $(btn).closest('tr');
  
    if (typeof ReadData === 'function') {
      myRowData = ReadData($(row));
    }
  
  
    if (typeof btnEditarUsuario_OnClick === 'function') {
      btnEditarUsuario_OnClick({
        Element: me,
        RowData: myRowData
      });
    }
  
} 


function __tcd(btn) {
    
    var me = $(btn)[0];
    var row = $(btn).closest('tr');

    if (typeof ReadData === 'function') {
        myRowData = ReadData($(row));
    }
 
    if (typeof btnDesactivarUsuario_OnClick === 'function') {
        btnDesactivarUsuario_OnClick({
        Element: me,
        RowData: myRowData
        });
    }

}




function validarInputsUsuario(frmkey, check_password = true){
    
    var statusInputNombre = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Nombre`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x.length >= 3 && x.length <= 20) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar un nombre valido.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 
    /* ------------------------------------------------------------------------------------------------ */




    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL CORREO DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    var statusInputCorreo = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-LoginName`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
        
            let email = validateFormatoEmail(x)

            if (email == null) {
                return false
            }

            if (email[5] == "sergioescobar.cl"){
                return true
            }else{
                false
            }

        }, /*  --- */
        '',
        'Ingrese correo con dominio sergioescobar.cl' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )
    /* ------------------------------------------------------------------------------------------------ */
    
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE LA CONTRASEÑA DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    if (check_password == true){
        var statusInputPassword = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Password`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            /* validamos el input ingresado */
            x = validarTextoInput(x)

            $(`${frmkey}-Password`).val(x)

            if (x.length < 8) { 
                return false;
            }
            return true;


        }, /*  --- */
        '',
        'La contraseña debe contener minimo 8 caracteres' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
        )
    }else{
        var statusInputPassword = true;
    }
  
   


    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE A LA SIGLA DEL USUASRIO                                       */
    /* ------------------------------------------------------------------------------------------------ */
    var statusInputSigla = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Sigla`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
            x   =   validarTextoInput(x)
            if (x.length != 2){
                return false;
            }else{
                return true;
            }

        }, /*  --- */
        '',
        'Ingrese sigla, 2 caracteres como máximo' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )
    /* ------------------------------------------------------------------------------------------------ */
    


    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL CARGO DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var statusInputCargo = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Cargo`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
            
            x = validarTextoInput(x);

            if (x.length >= 1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar campo.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 
    /* ------------------------------------------------------------------------------------------------ */




    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL CARGO DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var statusInputTipoUsuario = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-IdTipoUsuario`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
            if (x >= 1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Seleccione un tipo de usuario' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 
    /* ------------------------------------------------------------------------------------------------ */
        
    if  (
        statusInputNombre       != true ||
        statusInputCorreo       != true ||
        statusInputPassword     != true ||
        statusInputSigla        != true ||
        statusInputCargo        != true ||
        statusInputTipoUsuario  != true 
    ){
        return false;
    }else{
        return true;
    }
}