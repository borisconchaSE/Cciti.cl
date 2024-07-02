function btnEditarStock_OnClick(eventInfo){ 

    debugger;
    const PopupEditarStock = NewPopUp({
        dismissOnOutsideClick : true
    });
 

    var id_stock   =   eventInfo.RowData.id_stock
 
    var entity      =   {
        "id_stock" :   id_stock
    } 

    const service = new BotonesStockSvc(); 
 
    service.PopupEditarStock(
        entity,
        function(result){
            $(PopupEditarStock).RefreshPopUp(result);
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

function btnGuardarCambiosStock_OnClick(eventInfo){

    debugger;
    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnGuardarCambiosStock').html(); 
    $('#btnGuardarCambiosStock').addClass('disabled')

    $('#btnGuardarCambiosStock').html('<i class="fa fa-spinner fa-pulse"></i>');
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    
    var status =    validarInputsStock(frmkey)
    if (status != true){
        $('#btnGuardarCambiosStock').removeClass('disabled')
        $('#btnGuardarCambiosStock').html(txtbackup); 
        return; 
    }



    var id_stock                    =   eventInfo.FormData.id_stock;
    var Fecha                       =   eventInfo.FormData.Fecha;
    var Fecha_Asignacion            =   eventInfo.FormData.Fecha_Asignacion;
    var Descripcion                 =   eventInfo.FormData.Descripcion;
    var Cantidad                    =   eventInfo.FormData.Cantidad;
    var Precio_Unitario             =   eventInfo.FormData.Precio_Unitario;
    var Precio_total                =   eventInfo.FormData.Precio_total;
    var IdEmpresaU                  =   eventInfo.FormData.IdEmpresaU;
    var idDepto                     =   eventInfo.FormData.idDepto;
    var idubicacion                 =   eventInfo.FormData.idubicacion;
    var idMarca                     =   eventInfo.FormData.idMarca;
    var IdEmpresa                   =   eventInfo.FormData.IdEmpresa;
    var tipo                        =   eventInfo.FormData.tipo;
    var estado_stock                =   eventInfo.FormData.estado_stock;
    var idModelo                    =   eventInfo.FormData.idModelo;
    var idCentro                    =   eventInfo.FormData.idCentro;


     
    var entity      =   {
        "DatosStock"  :   JSON.stringify({

            id_stock                        :   id_stock,
            Fecha                           :   Fecha,
            Descripcion                     :   Descripcion,
            Cantidad                        :   Cantidad,
            Precio_Unitario                 :   Precio_Unitario,
            Precio_total                    :   Precio_total,
            IdEmpresaU                      :   IdEmpresaU,
            idDepto                         :   idDepto,
            idubicacion                     :   idubicacion,
            idMarca                         :   idMarca,
            IdEmpresa                       :   IdEmpresa,
            tipo                            :   tipo,
            estado_stock                    :   estado_stock,
            idModelo                        :   idModelo,
            Fecha_Asignacion                :   Fecha_Asignacion,
            idCentro                        :   idCentro
        })
    }

    const service = new BotonesStockSvc(); 
 
    service.CambiarParametrosStock(
        entity,
        function(result){
            if(result.IdEmpresaU == null || result.idDepto == null || result.idubicacion == null){
                $(`td[data-pk="${id_stock}"][data-property-name="Fecha_Llegada"]`).html(Fecha)
                $(`td[data-pk="${id_stock}"][data-property-name="Nombre_Producto"]`).html(Descripcion)
                $(`td[data-pk="${id_stock}"][data-property-name="Cantidad"]`).html(Cantidad)
                $(`td[data-pk="${id_stock}"][data-property-name="Precio_Unitario"]`).html(Precio_Unitario)
                $(`td[data-pk="${id_stock}"][data-property-name="Marca"]`).html(idMarca)
                $(`td[data-pk="${id_stock}"][data-property-name="Modelo"]`).html(idModelo)
                $(`td[data-pk="${id_stock}"][data-property-name="Empresa"]`).html(IdEmpresa)
                $(`td[data-pk="${id_stock}"][data-property-name="Tipo_tonner"]`).html(tipo)
                $(`td[data-pk="${id_stock}"][data-property-name="Estado_Producto"]`).html(result.estado_stock)
            }else{
                var row = $('#tbListadoStock').DataTable().row($(id_stock).parents('tr'));
                row.remove();
            }  
            $('.modal').modal('hide');  
            $('#tbListadoStock').DataTable().ajax.reload()
            Toast.fire({
                icon: 'success',
                title: 'Cambios guardados correctamente'
            });
        },
        function(errorCode,errorMessage){ 
            $('#btnGuardarCambiosStock').removeClass('disabled')
            $('#btnGuardarCambiosStock').html(txtbackup); 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )
}

function validarInputsStock(frmkey){
    
    var statusInputProducto = ValidarInput(
        true,
        `${frmkey}-Descripcion`, 
        function(x){

            x = validarTextoInput(x);

            if (x != undefined && x != null && x != "") {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar un producto.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 
    /* ------------------------------------------------------------------------------------------------ */


    /* ------------------------------------------------------------------------------------------------ */
    
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE LA CONTRASEÑA DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    var statusInputFecha = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-Fecha`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            /* validamos el input ingresado */
            x = validarTextoInput(x)

            $(`${frmkey}-Fecha`).val(x)

            y = x.length;

            if (y == 10) { 
                return true;
            }
            else{
                return false;
            }


        }, /*  --- */
        '',
        'La fecha no tiene el formato correcto.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )

  
 


    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL CARGO DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var statusInputCantidad = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-Cantidad`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */
            
            x = validarTextoInput(x);


            if (x != undefined && x != null && x != "") {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar cuantos productos va a agregar.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 
    /* ------------------------------------------------------------------------------------------------ */


    var statusInputPrecioU = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-Precio_Unitario`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if(x == 1){
                return true;
            }
            else if (x != undefined && x != null && x.length >= 3) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar el precio unitario.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )
    
    var statusInputPrecioT = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-Precio_total`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != undefined && x != null && x.length >= 3) {
                return true;
            }else{
                return false;
            }
            
        }, /*  --- */
        '',
        'Ingresar el valor total.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )

    var statusInputEmpresa = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-IdEmpresa`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar una empresa disponible.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 

    var statusInputMarca = ValidarInput( /* Invocamos la función que permite validar los inputs */
        true,
        `${frmkey}-idMarca`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar una marca disponible.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 

    /* ------------------------------------------------------------------------------------------------ */
        
    if  (
        statusInputProducto         != true ||
        statusInputFecha            != true ||
        statusInputCantidad         != true ||
        statusInputPrecioU          != true ||
        statusInputPrecioT          != true ||
        statusInputEmpresa          != true ||
        statusInputMarca            != true 
    ){
        return false;
    }else{
        return true;
    }
}

function frmEditarStock_estado_stock_OnChange(eventInfo) {

    var estado_stock = eventInfo.FormData.estado_stock;

    if (estado_stock == "Entregado"){
        $('#frmEditarStock-IdEmpresaU').prop('disabled', false);
        $('#frmEditarStock-idDepto').prop('disabled', false);
        $('#frmEditarStock-idubicacion').prop('disabled', false);
        $('#frmEditarStock-idCentro').prop('disabled', false);
    }else{
        $('#frmEditarStock-IdEmpresaU').prop('disabled', true);
        $('#frmEditarStock-idDepto').prop('disabled', true);
        $('#frmEditarStock-idubicacion').prop('disabled', true);
        $('#frmEditarStock-idCentro').prop('disabled', true);
    }
}

function frmEditarStock_IdEmpresaU_OnChange(eventInfo) {

    var IdEmpresaU = eventInfo.FormData.IdEmpresaU;

    if (IdEmpresaU != -1){
        var entity      =   {
            "DatosEmpresa"  :   JSON.stringify({
    
                IdEmpresaU                      :   IdEmpresaU
            })
        }
        
        const service = new OpcionesStockSvc();
    
        console.log(entity)
     
        service.GetbyEmpresa(
            entity,
            function(result){
                debugger;
                console.log(result);
                $('#frmEditarStock-idDepto').html("")
                var optios = ""
                optios += `<option value="-1">Sin Seleccionar</option>`
                $.each( result, function( key, value ) {
                    optios += `<option value="${value.idDepto}">${value.Descripcion}</option>`
                })
                $('#frmEditarStock-idDepto').html(optios);
            },
            function(errorCode,errorMessage){ 
                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
            }
        )
    }
}

function frmEditarStock_idDepto_OnChange(eventInfo) {

    var idDepto = eventInfo.FormData.idDepto;

    if(idDepto != -1){
        var entity      =   {
            "DatosDepto"  :   JSON.stringify({
    
                idDepto                      :   idDepto
            })
        }
        
        const service = new OpcionesStockSvc(); 
     
        service.GetByDepto(
            entity,
            function(result){
                debugger;
                console.log(result);
                $('#frmEditarStock-idubicacion').html("")
                var optios = ""
                optios += `<option value="-1">Sin Seleccionar</option>`
                $.each( result, function( key, value ) {
                    optios += `<option value="${value.idubicacion}">${value.Descripcion}</option>`
                })
                $('#frmEditarStock-idubicacion').html(optios);
            },
            function(errorCode,errorMessage){ 
                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
            }
        )
    }
}

function frmEditarStock_idubicacion_OnChange(eventInfo) {

    var idubicacion = eventInfo.FormData.idubicacion;
    var idDepto = eventInfo.FormData.idDepto;

    if(idubicacion != -1){
        var entity      =   {
            "DatosUbi"  :   JSON.stringify({
    
                idubicacion                     :   idubicacion,
                idDepto                         :   idDepto
            })
        }
        
        const service = new OpcionesStockSvc(); 
     
        service.GetByUbi(
            entity,
            function(result){
                debugger;
                console.log(result);
                $('#frmEditarStock-idCentro').html("")
                var optios = ""
                optios += `<option value="-1">Sin Seleccionar</option>`
                $.each( result, function( key, value ) {
                    optios += `<option value="${value.idCentro}">${value.Descripcion}</option>`
                })
                $('#frmEditarStock-idCentro').html(optios);
            },
            function(errorCode,errorMessage){ 
                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
            }
        )
    }
}

function __obtener_idx(){
    var   basename            =   $(location).attr('origin');
    var   href                =   $(location).attr('href').replace(basename,"");
    return  href.replace("/stock/","");
}

function __tceu(btn){

    var me = $(btn)[0];
    var row = $(btn).closest('tr');
  
    if (typeof ReadData === 'function') {
      myRowData = ReadData($(row));
    }
  
  
    if (typeof btnEditarStock_OnClick === 'function') {
        btnEditarStock_OnClick({
            Element: me,
            RowData: myRowData
        });
    }
  
} 