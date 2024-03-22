function btnNuevaCompra_OnClick(eventInfo){

    debugger;

    var identificador   =   eventInfo.Element.innerText;

    const PopupAgregarProducto = NewPopUp({
        dismissOnOutsideClick : true
    }); 
 
    var entity      =   {
        "identificador" :   identificador
    } 

    const service = new BotonesCompraSvc(); 
 
    service.PopupAgregarProducto(
        entity,
        function(result){
            $(PopupAgregarProducto).RefreshPopUp(result);
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




function btnEditarCompra_OnClick(eventInfo){ 

 
    debugger;
    
    const PopupEditarCompra = NewPopUp({
        dismissOnOutsideClick : true
    });
 

    var ido_c   =   eventInfo.RowData.ido_c
 
    var entity      =   {
        "ido_c" :   ido_c
    } 

    const service = new BotonesCompraSvc(); 
 
    service.PopupEditarCompra(
        entity,
        function(result){
            $(PopupEditarCompra).RefreshPopUp(result);
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



function btnCrearNuevaCompra_OnClick(eventInfo){
    debugger;
    console.log(eventInfo);

    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnCrearNuevaCompra').html(); 
    
    $('#btnCrearNuevaCompra').addClass('disabled') 
    $('#btnCrearNuevaCompra').html('<i class="fa fa-spinner fa-pulse"></i>');

    
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var status =    validarInputsCompra(frmkey)
    if (status != true){
        $('#btnCrearNuevaCompra').html(txtbackup); 
        $('#btnCrearNuevaCompra').removeClass('disabled') 
        return; 
    } 
     
    /* ------------------------------------------------------------------------------------------------ */

    var entity      =   {
        "NuevoCompra"  :  JSON.stringify({
            "Fecha_compra"          :   eventInfo.FormData.Fecha_compra,
            "Descripcion"           :   eventInfo.FormData.Descripcion,
            "idMarca"               :   eventInfo.FormData.idMarca,
            "idModelo"              :   eventInfo.FormData.idModelo,
            "Orden_compra"          :   eventInfo.FormData.Orden_compra,
            "Factura_compra"        :   eventInfo.FormData.Factura_compra,
            "Precio_U"              :   eventInfo.FormData.Precio_U,
            "Cantidad"              :   eventInfo.FormData.Cantidad,
            "Precio_total"          :   eventInfo.FormData.Precio_total,
            "tipo"                  :   eventInfo.FormData.tipo,
            "idProveedor"           :   eventInfo.FormData.idProveedor,
            "idEstado_oc"           :   eventInfo.FormData.idEstado_oc,
            "idEstado_FC"           :   eventInfo.FormData.idEstado_FC,
            "IdEmpresa"             :   eventInfo.FormData.IdEmpresa
        })
    } 

    const service = new BotonesCompraSvc(); 
 
    service.GuardarNuevoProducto(
        entity,
        function(result){
            console.log(result);
            DibujarNuevoCompra(result);
            $('.modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Producto agregador correctamente'
            });
            location.reload(true);
        },
        function(errorCode,errorMessage){ 
            $('#btnCrearNuevaCompra').html(txtbackup); 
            $('#btnCrearNuevaCompra').removeClass('disabled') 
         
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
function DibujarNuevoCompra(DatosCompra = {
    'Fecha_compra'              :   '2000-01-01',
    'Descripcion'               :   'HP30A',
    'idMarca'                   :   'HP',
    'idModelo'                  :   '30A',
    'Orden_compra'              :   '16513',
    'Factura_compra'            :   '185202',
    'Precio_U'                  :   '58632',
    'Cantidad'                  :   '1',
    'Precio_total'              :   '58632',
    'tipo'                      :   'Original',
    'idProveedor'               :   'Comercial Rann LTDA',
    'idEstado_oc'               :   'Emitida',
    'idEstado_FC'               :   'Contabilizada',
    'IdEmpresa'                 :   'SEM',
}, Permisos = []){
 
    var table       = $('#tbListadoCompras').DataTable();

    var idCell              =   DatosCompra.idO_C;
    var Fecha               =   DatosCompra.Fecha_compra;
    var Descripcion         =   DatosCompra.Descripcion;
    var idMarca             =   DatosCompra.idMarca;
    var idModelo            =   DatosCompra.idModelo;
    var Orden_compra        =   DatosCompra.Orden_compra;
    var Factura_compra      =   DatosCompra.Factura_compra;
    var Precio_U            =   DatosCompra.Precio_U;
    var Cantidad            =   DatosCompra.Cantidad;
    var Precio_total        =   DatosCompra.Precio_total;
    var tipo                =   DatosCompra.tipo;
    var idProveedor         =   DatosCompra.idProveedor;
    var idEstado_oc         =   DatosCompra.idEstado_oc;
    var idEstado_FC         =   DatosCompra.idEstado_FC;
    var IdEmpresa           =   DatosCompra.IdEmpresa;   


    var rowNode     = table.row.add( [ idCell, Fecha, Descripcion, idMarca, idModelo, Orden_compra, Factura_compra, Precio_U, Cantidad, Precio_total, tipo, idProveedor, idEstado_oc, idEstado_FC, IdEmpresa] ).draw().node();
    
    /* AGREGAMOS LOS VALORES PK */
    debugger;
    $(rowNode).attr('data-pk',DatosCompra.idO_C)
    $(rowNode).attr('data-idcompra',DatosCompra.idO_C) 

    /* AGREGAMOS LOS IDS QUE CORRESPONDEN */
    $(rowNode).find('td:eq(0)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(1)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(2)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(3)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(4)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(5)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(6)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(7)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(8)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(9)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(10)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(11)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(12)').attr('data-pk',DatosCompra.idO_C)
    $(rowNode).find('td:eq(13)').attr('data-pk',DatosCompra.idO_C)

    /* INCORPORAMOS EL NOMBRE DE LA PROPIEDAD A LA TABLA */
    $(rowNode).find('td:eq(0)').attr('data-property-name','Fecha_compra')
    $(rowNode).find('td:eq(1)').attr('data-property-name','Descripcion')
    $(rowNode).find('td:eq(2)').attr('data-property-name','idMarca')
    $(rowNode).find('td:eq(3)').attr('data-property-name','idModelo')
    $(rowNode).find('td:eq(4)').attr('data-property-name','Orden_compra')
    $(rowNode).find('td:eq(5)').attr('data-property-name','Factura_compra')
    $(rowNode).find('td:eq(6)').attr('data-property-name','Precio_U')
    $(rowNode).find('td:eq(7)').attr('data-property-name','Cantidad')
    $(rowNode).find('td:eq(8)').attr('data-property-name','Precio_total')
    $(rowNode).find('td:eq(9)').attr('data-property-name','tipo')
    $(rowNode).find('td:eq(10)').attr('data-property-name','idProveedor')
    $(rowNode).find('td:eq(11)').attr('data-property-name','idEstado_oc')
    $(rowNode).find('td:eq(12)').attr('data-property-name','idEstado_FC')
    $(rowNode).find('td:eq(13)').attr('data-property-name','IdEmpresa')

}

function btnGuardarCambiosCompra_OnClick(eventInfo){

    debugger;
    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnGuardarCambiosCompra').html(); 
    $('#btnGuardarCambiosCompra').addClass('disabled')

    $('#btnGuardarCambiosCompra').html('<i class="fa fa-spinner fa-pulse"></i>');
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
    
    var status =    validarInputsCompra(frmkey)
    if (status != true){
        $('#btnGuardarCambiosCompra').removeClass('disabled')
        $('#btnGuardarCambiosCompra').html(txtbackup); 
        return; 
    }



    var idO_C                       =   eventInfo.FormData.idO_C;
    var Fecha_compra                =   eventInfo.FormData.Fecha_compra;
    var Descripcion                 =   eventInfo.FormData.Descripcion;
    var idMarca                     =   eventInfo.FormData.idMarca;
    var idModelo                    =   eventInfo.FormData.idModelo;
    var Orden_compra                =   eventInfo.FormData.Orden_compra;
    var Factura_compra              =   eventInfo.FormData.Factura_compra;
    var Precio_U                    =   eventInfo.FormData.Precio_U;
    var Cantidad                    =   eventInfo.FormData.Cantidad;
    var Precio_total                =   eventInfo.FormData.Precio_total;
    var tipo                        =   eventInfo.FormData.tipo;
    var idProveedor                 =   eventInfo.FormData.idProveedor;
    var idEstado_oc                 =   eventInfo.FormData.idEstado_oc;
    var idEstado_FC                 =   eventInfo.FormData.idEstado_FC;
    var IdEmpresa                   =   eventInfo.FormData.IdEmpresa;
 

     
    var entity      =   {
        "DatosCompra"  :   JSON.stringify({

            idO_C                           :   idO_C,
            Fecha_compra                    :   Fecha_compra,
            Descripcion                     :   Descripcion,
            idMarca                         :   idMarca,
            idModelo                        :   idModelo,
            Orden_compra                    :   Orden_compra,
            Factura_compra                  :   Factura_compra,
            Precio_U                        :   Precio_U,
            Cantidad                        :   Cantidad,
            Precio_total                    :   Precio_total,
            tipo                            :   tipo,
            idProveedor                     :   idProveedor,
            idEstado_oc                     :   idEstado_oc,
            idEstado_FC                     :   idEstado_FC,
            IdEmpresa                       :   IdEmpresa
        })
    }

    const service = new BotonesCompraSvc(); 
 
    service.CambiarParametrosCompra(
        entity,
        function(result){  
            $('.modal').modal('hide');  
            $(`td[data-pk="${idO_C}"][data-property-name="Fecha_compra"]`).html(Fecha_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Descripcion"]`).html(Descripcion)
            $(`td[data-pk="${idO_C}"][data-property-name="marca"]`).html(idMarca)
            $(`td[data-pk="${idO_C}"][data-property-name="modelo"]`).html(idModelo)
            $(`td[data-pk="${idO_C}"][data-property-name="Orden_compra"]`).html(Orden_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Factura_compra"]`).html(Factura_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Precio_U"]`).html(Precio_U)
            $(`td[data-pk="${idO_C}"][data-property-name="Cantidad"]`).html(Cantidad)
            $(`td[data-pk="${idO_C}"][data-property-name="Precio_total"]`).html(Precio_total)
            $(`td[data-pk="${idO_C}"][data-property-name="tipo"]`).html(tipo)
            $(`td[data-pk="${idO_C}"][data-property-name="idProveedor"]`).html(idProveedor)
            $(`td[data-pk="${idO_C}"][data-property-name="idEstado_oc"]`).html(idEstado_oc)
            $(`td[data-pk="${idO_C}"][data-property-name="idEstado_FC"]`).html(idEstado_FC)
            $(`td[data-pk="${idO_C}"][data-property-name="IdEmpresa"]`).html(IdEmpresa)
            Toast.fire({
                icon: 'success',
                title: 'Cambios guardados correctamente'
            });
            location.reload(true); 
        },
        function(errorCode,errorMessage){ 
            $('#btnGuardarCambiosCompra').removeClass('disabled')
            $('#btnGuardarCambiosCompra').html(txtbackup); 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )
}


function validarInputsCompra(frmkey){
    
    var statusInputProducto = ValidarInput( /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Descripcion`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

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
        `${frmkey}-Fecha_compra`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            /* validamos el input ingresado */
            x = validarTextoInput(x)

            $(`${frmkey}-Fecha_compra`).val(x)
            
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
        `${frmkey}-Precio_U`, /* facilitamos el ID del input que debemos validar */
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

    var statusInputModelo = ValidarInput( /* Invocamos la función que permite validar los inputs */
    `${frmkey}-idModelo`, /* facilitamos el ID del input que debemos validar */
    function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

        x = validarTextoInput(x);

        if (x != -1) {
            return true;
        }else{
            return false;
        }
    }, /*  --- */
    '',
    'Ingresar un modelo disponible.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )

    var statusInputProveedor = ValidarInput( /* Invocamos la función que permite validar los inputs */
    `${frmkey}-idProveedor`, /* facilitamos el ID del input que debemos validar */
    function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

        x = validarTextoInput(x);

        if (x != -1) {
            return true;
        }else{
            return false;
        }
    }, /*  --- */
    '',
    'Ingresar un proveedor disponible.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    ) 

    /* ------------------------------------------------------------------------------------------------ */
        
    if  (
        statusInputProducto         != true ||
        statusInputFecha            != true ||
        statusInputCantidad         != true ||
        statusInputPrecioU          != true ||
        statusInputPrecioT          != true ||
        statusInputEmpresa          != true ||
        statusInputModelo           != true ||
        statusInputProveedor        != true ||
        statusInputMarca            != true 
    ){
        return false;
    }else{
        return true;
    }
}

function __tceu(btn){

    var me = $(btn)[0];
    var row = $(btn).closest('tr');
  
    if (typeof ReadData === 'function') {
      myRowData = ReadData($(row));
    }
  
  
    if (typeof btnEditarCompra_OnClick === 'function') {
        btnEditarCompra_OnClick({
            Element: me,
            RowData: myRowData
        });
    }
  
} 