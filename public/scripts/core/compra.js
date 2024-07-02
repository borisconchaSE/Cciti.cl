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
    'Fecha_Compra'                  :   '2000-01-01',
    'Nombre_Producto'               :   'HP30A',
    'Marca'                         :   'HP',
    'Modelo'                        :   '30A',
    'Orden_Compra'                  :   '16513',
    'Factura_Compra'                :   '185202',
    'Precio_Unitario'               :   '58632',
    'Cantidad'                      :   '1',
    'Precio_Total'                  :   '58632',
    'Tipo'                          :   'Original',
    'Proveedor'                     :   'Comercial Rann LTDA',
    'Estado_OC'                     :   'Emitida',
    'Estado_FC'                     :   'Contabilizada',
    'Empresa'                       :   'SEM',
}, Permisos = []){
 
    var table       = $('#tbListadoCompras').DataTable();

    var Fecha_Compra            =   DatosCompra.Fecha_Compra;
    var Nombre_Producto         =   DatosCompra.Nombre_Producto;
    var Marca                   =   DatosCompra.Marca;
    var Modelo                  =   DatosCompra.Modelo;
    var Orden_Compra            =   DatosCompra.Orden_Compra;
    var Factura_Compra          =   DatosCompra.Factura_Compra;
    var Precio_Unitario         =   DatosCompra.Precio_Unitario;
    var Cantidad                =   DatosCompra.Cantidad;
    var Precio_Total            =   DatosCompra.Precio_Total;
    var Tipo                    =   DatosCompra.Tipo;
    var Proveedor               =   DatosCompra.Proveedor;
    var Estado_OC               =   DatosCompra.Estado_OC;
    var Estado_FC               =   DatosCompra.Estado_FC;
    var Empresa                 =   DatosCompra.Empresa;
    var Boton                   =   `<div class="text-right one-line-text">
                                        <button class="btnEditarCompra btn  btn btn-sm btn-success" style="position: relative;  cursor: pointer;  " id="btnEditarCompra" name="btnEditarCompra" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Editar" data-table="tbListadoCompras">
                                            <i class="fa fa-edit"></i>
                                        <div class="hide badge-element" style="position: absolute;  right: 2px;  top: -10px;  cursor: pointer; " id="btnEditarCompraBadge">
                                            <span class="badge bg-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="btnEditarCompraBadgeContent">
                                            </span>
                                        </div>
                                        </button>
                                    </div>`;           


    var rowNode     = table.row.add( [Fecha_Compra, Nombre_Producto, Marca, Modelo, Orden_Compra, Factura_Compra, Precio_Unitario, Cantidad, Precio_Total, Tipo, Proveedor, Estado_OC, Estado_FC, Empresa,Boton] ).draw().node();
    
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
    $(rowNode).find('td:eq(14)').attr('data-pk',DatosCompra.idO_C)

    /* INCORPORAMOS EL NOMBRE DE LA PROPIEDAD A LA TABLA */
    $(rowNode).find('td:eq(0)').attr('data-property-name','Fecha_Compra')
    $(rowNode).find('td:eq(1)').attr('data-property-name','Nombre_Producto')
    $(rowNode).find('td:eq(2)').attr('data-property-name','Marca')
    $(rowNode).find('td:eq(3)').attr('data-property-name','Modelo')
    $(rowNode).find('td:eq(4)').attr('data-property-name','Orden_Compra')
    $(rowNode).find('td:eq(5)').attr('data-property-name','Factura_Compra')
    $(rowNode).find('td:eq(6)').attr('data-property-name','Precio_Unitario')
    $(rowNode).find('td:eq(7)').attr('data-property-name','Cantidad')
    $(rowNode).find('td:eq(8)').attr('data-property-name','Precio_Total')
    $(rowNode).find('td:eq(9)').attr('data-property-name','Tipo')
    $(rowNode).find('td:eq(10)').attr('data-property-name','Proveedor')
    $(rowNode).find('td:eq(11)').attr('data-property-name','Estado_OC')
    $(rowNode).find('td:eq(12)').attr('data-property-name','Estado_FC')
    $(rowNode).find('td:eq(13)').attr('data-property-name','Empresa')

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
    var Fecha_compra                =   eventInfo.FormData.Fecha_Compra;
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
            $(`td[data-pk="${idO_C}"][data-property-name="Fecha_Compra"]`).html(Fecha_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Nombre_Producto"]`).html(Descripcion)
            $(`td[data-pk="${idO_C}"][data-property-name="Marca"]`).html(idMarca)
            $(`td[data-pk="${idO_C}"][data-property-name="Modelo"]`).html(idModelo)
            $(`td[data-pk="${idO_C}"][data-property-name="Orden_Compra"]`).html(Orden_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Factura_Compra"]`).html(Factura_compra)
            $(`td[data-pk="${idO_C}"][data-property-name="Precio_Unitario"]`).html(Precio_U)
            $(`td[data-pk="${idO_C}"][data-property-name="Cantidad"]`).html(Cantidad)
            $(`td[data-pk="${idO_C}"][data-property-name="Precio_Total"]`).html(Precio_total)
            $(`td[data-pk="${idO_C}"][data-property-name="Tipo"]`).html(tipo)
            $(`td[data-pk="${idO_C}"][data-property-name="Proveedor"]`).html(idProveedor)
            $(`td[data-pk="${idO_C}"][data-property-name="Estado_OC"]`).html(idEstado_oc)
            $(`td[data-pk="${idO_C}"][data-property-name="Estado_FC"]`).html(idEstado_FC)
            $(`td[data-pk="${idO_C}"][data-property-name="Empresa"]`).html(IdEmpresa)
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
    
    var statusInputProducto = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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
    var statusInputFecha = ValidarInput(
        true,/* Invocamos la función que permite validar los inputs */
        `${frmkey}-Fecha_Compra`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            /* validamos el input ingresado */
            x = validarTextoInput(x)

            $(`${frmkey}-Fecha_Compra`).val(x)
            
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
 
    var statusInputCantidad = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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


    var statusInputPrecioU = ValidarInput( 
        true,/* Invocamos la función que permite validar los inputs */
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
    
    var statusInputPrecioT = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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

    var statusInputEmpresa = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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

    var statusInputMarca = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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

    var statusInputModelo = ValidarInput(
        true,     /* Invocamos la función que permite validar los inputs */
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

        var statusInputProveedor = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
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
    
    var statusInputOrden = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Orden_compra`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar orden de compra.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )
    
    var statusInputFactura = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
        `${frmkey}-Factura_compra`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar factura de compra.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )
    
    var statusInputTipo = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
        `${frmkey}-tipo`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar tipo producto.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )

    var statusInputOC = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
        `${frmkey}-idEstado_oc`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar estado OC.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
    )

    var statusInputFC = ValidarInput(
        true, /* Invocamos la función que permite validar los inputs */
        `${frmkey}-idEstado_FC`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            x = validarTextoInput(x);

            if (x != -1) {
                return true;
            }else{
                return false;
            }
        }, /*  --- */
        '',
        'Ingresar estado FC.' /* Ingresamos el mensaje que se debe mostrar en caso de ser invalido */
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
        statusInputMarca            != true ||
        statusInputOrden            != true ||
        statusInputFactura          != true ||
        statusInputTipo             != true ||
        statusInputOC               != true ||
        statusInputFC               != true 
    ){
        return false;
    }else{
        return true;
    }
}

function frmNuevoProducto_idMarca_OnChange(eventInfo) {

    var idMarca = eventInfo.FormData.idMarca;

    if (idMarca != -1){
        var entity      =   {
            "DatosMarca"  :   JSON.stringify({
    
                idMarca                      :   idMarca
            })
        }
        
        const service = new OpcionesCompraSvc();
    
        console.log(entity)
     
        service.GetbyMarca(
            entity,
            function(result){
                debugger;
                console.log(result);
                $('#frmNuevoProducto-idModelo').html("")
                var optios = ""
                optios += `<option value="-1">Sin Seleccionar</option>`
                $.each( result, function( key, value ) {
                    optios += `<option value="${value.idModelo}">${value.Descripcion}</option>`
                })
                $('#frmNuevoProducto-idModelo').html(optios);
                $('#frmNuevoProducto-idModelo').prop('disabled', false);
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

function frmNuevoProducto_Precio_U_OnKeyUp(eventInfo){
    var precio_total = obtenertotal(eventInfo)
    $('#frmNuevoProducto-Precio_total').html("")
    $('#frmNuevoProducto-Precio_total').prop('value', precio_total);
}

function frmNuevoProducto_Cantidad_OnKeyUp(eventInfo){
    var precio_total = obtenertotal(eventInfo)
    $('#frmNuevoProducto-Precio_total').html("")
    $('#frmNuevoProducto-Precio_total').prop('value', precio_total);
}

function frmEditarCompra_Precio_U_OnKeyUp(eventInfo){
    var precio_total = obtenertotal(eventInfo)
    $('#frmEditarCompra-Precio_total').html("")
    $('#frmEditarCompra-Precio_total').prop('value', precio_total);
}

function frmEditarCompra_Cantidad_OnKeyUp(eventInfo){
    var precio_total = obtenertotal(eventInfo)
    $('#frmEditarCompra-Precio_total').html("")
    $('#frmEditarCompra-Precio_total').prop('value', precio_total);
}

function obtenertotal(eventInfo){
    var b = eventInfo.FormData.Precio_U;
    var a = eventInfo.FormData.Cantidad;

    var total = b * a;
    return total;

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