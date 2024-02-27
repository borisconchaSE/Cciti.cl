function btnNuevaCompra_OnClick(){

    const PopupAgregarProducto = NewPopUp({
        dismissOnOutsideClick : true
    }); 
 

    const service = new BotonesCompraSvc(); 
 
    service.PopupAgregarProducto(
        null,
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

 
    const PopupEditarCompra = NewPopUp({
        dismissOnOutsideClick : true
    });
 

    var idO_C   =   eventInfo.RowData.idO_C
 
    var entity      =   {
        "IdO_C" :   idO_C
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
            "idO_C"                 :   eventInfo.FormData.idO_C,
            "Fecha_compra"          :   eventInfo.FormData.Fecha_compra,
            "Descripcion"           :   eventInfo.FormData.Descripcion,
            "marca"                 :   eventInfo.FormData.marca,
            "modelo"                :   eventInfo.FormData.modelo,
            "Orden_compra"          :   eventInfo.FormData.Orden_compra,
            "Factura_compra"        :   eventInfo.FormData.Factura_compra,
            "Precio_U"              :   eventInfo.FormData.Precio_U,
            "Cantidad"              :   eventInfo.FormData.Cantidad,
            "Precio_total"          :   eventInfo.FormData.Precio_total,
            "tipo"                  :   eventInfo.FormData.tipo,
            "Proveedor_idProveedor" :   eventInfo.FormData.Proveedor_idProveedor,
            "id_estadoOC"           :   eventInfo.FormData.id_estadoOC,
            "id_estadoFC"           :   eventInfo.FormData.id_estadoFC,
            "id_empresa"            :   eventInfo.FormData.id_empresa
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
    'idO_C'                     :   999,
    'Fecha_compra'              :   '01-01-2000',
    'Descripcion'               :   'HP30A',
    'marca'                     :   'HP',
    'modelo'                    :   '30A',
    'Orden_compra'              :   '16513',
    'Factura_compra'            :   '185202',
    'Precio_U'                  :   '58632',
    'Cantidad'                  :   '1',
    'Precio_total'              :   '58632',
    'tipo'                      :   'Original',
    'Proveedor_idProveedor'     :   'Comercial Rann LTDA',
    'id_estadoOC'               :   'Emitida',
    'id_estadoFC'               :   'Contabilizada',
    'id_empresa'                :   'SEM',
}, Permisos = []){
 
    var table       = $('#tbListadoCompras').DataTable();

    var idCell              =   DatosCompra.idO_C;
    var Fecha               =   DatosCompra.Fecha_compra;
    var Descripcion         =   DatosCompra.Descripcion;
    var marca               =   DatosCompra.marca;
    var modelo              =   DatosCompra.modelo;
    var Orden_compra        =   DatosCompra.Orden_compra;
    var Factura_compra      =   DatosCompra.Factura_compra;
    var Precio_U            =   DatosCompra.Precio_U;
    var Cantidad            =   DatosCompra.Cantidad;
    var Precio_total        =   DatosCompra.Precio_total;
    var tipo                =   DatosCompra.tipo;
    var Proveedor           =   DatosCompra.Proveedor_idProveedor;
    var EstadoOC            =   DatosCompra.id_estadoOC;
    var EstadoFC            =   DatosCompra.id_estadoFC;
    var Empresa             =   DatosCompra.id_empresa;   


    var rowNode     = table.row.add( [ idCell, Fecha, Descripcion, marca, modelo, Orden_compra, Factura_compra, Precio_U, Cantidad, Precio_total, tipo, Proveedor, EstadoOC, EstadoFC, Empresa] ).draw().node();
    
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
    $(rowNode).find('td:eq(0)').attr('data-property-name','idO_C')
    $(rowNode).find('td:eq(1)').attr('data-property-name','Fecha_compra')
    $(rowNode).find('td:eq(2)').attr('data-property-name','Descripcion')
    $(rowNode).find('td:eq(3)').attr('data-property-name','marca')
    $(rowNode).find('td:eq(4)').attr('data-property-name','modelo')
    $(rowNode).find('td:eq(5)').attr('data-property-name','Orden_compra')
    $(rowNode).find('td:eq(6)').attr('data-property-name','Factura_compra')
    $(rowNode).find('td:eq(7)').attr('data-property-name','Precio_U')
    $(rowNode).find('td:eq(8)').attr('data-property-name','Cantidad')
    $(rowNode).find('td:eq(9)').attr('data-property-name','Precio_total')
    $(rowNode).find('td:eq(10)').attr('data-property-name','tipo')
    $(rowNode).find('td:eq(11)').attr('data-property-name','Proveedor')
    $(rowNode).find('td:eq(12)').attr('data-property-name','EstadoOC')
    $(rowNode).find('td:eq(13)').attr('data-property-name','EstadoFC')
    $(rowNode).find('td:eq(14)').attr('data-property-name','Empresa')

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

            if (x.length < 8 || x != null) { 
                return false;
            }
            return true;


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
        `${frmkey}-marca`, /* facilitamos el ID del input que debemos validar */
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

    var statusInputMarca = ValidarInput( /* Invocamos la función que permite validar los inputs */
    `${frmkey}-modelo`, /* facilitamos el ID del input que debemos validar */
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

    var statusInputMarca = ValidarInput( /* Invocamos la función que permite validar los inputs */
    `${frmkey}-Proveedor`, /* facilitamos el ID del input que debemos validar */
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
        statusInputMarca            != true 
    ){
        return false;
    }else{
        return true;
    }
}