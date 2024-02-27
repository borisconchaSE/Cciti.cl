function btnAgregarStock_OnClick(){

    const PopupAgregarStock = NewPopUp({
        dismissOnOutsideClick : true
    }); 
 

    const service = new BotonesStockSvc();
 
    service.PopupAgregarStock(
        null,
        function(result){
            $(PopupAgregarStock).RefreshPopUp(result);
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

function btnAgregarNuevoStock_OnClick(eventInfo){
    debugger;
    console.log(eventInfo);

    /* almacenamos en variables los datos que vamos a utilizar */
    var frmkey      =   eventInfo.FormKey; 

    var txtbackup   =   $('#btnAgregarNuevoStock').html(); 
    
    $('#btnAgregarNuevoStock').addClass('disabled') 
    $('#btnAgregarNuevoStock').html('<i class="fa fa-spinner fa-pulse"></i>');

    
    /* ------------------------------------------------------------------------------------------------ */
    /* VALIDAMOS EL INPUT QUE CORRESPONDE AL NOMBRE DEL NUEVO USUARIO                                   */
    /* ------------------------------------------------------------------------------------------------ */
 
    var status =    validarInputsStock(frmkey)
    if (status != true){
        $('#btnAgregarNuevoStock').html(txtbackup); 
        $('#btnAgregarNuevoStock').removeClass('disabled') 
        return; 
    } 
     
    /* ------------------------------------------------------------------------------------------------ */
    console.log('todo ok')

    var entity      =   {
        "NuevoStock"  :  JSON.stringify({
            "Fecha_asignacion"      :   eventInfo.FormData.Fecha_asignacion,
            "Descripcion"           :   eventInfo.FormData.Descripcion,
            "Cantidad"              :   eventInfo.FormData.Cantidad,
            "Precio_Unitario"       :   eventInfo.FormData.Precio_Unitario,
            "Precio_total"          :   eventInfo.FormData.Precio_total,
            "idMarca"               :   eventInfo.FormData.IdMarca,
            "IdEmpresa"             :   eventInfo.FormData.IdEmpresa,
            "tipo"                  :   eventInfo.FormData.tipo,
            "estado_stock"          :   eventInfo.FormData.estado_stock,
        })
    } 

    const service = new BotonesStockSvc(); 
 
    service.GuardarStockNuevo(
        entity,
        function(result){
            console.log(result);
            DibujarNuevoStock(result);
            $('.modal').modal('hide');
            Toast.fire({
                icon: 'success',
                title: 'Stock Agregado Correctamente'
            });
        },
        function(errorCode,errorMessage){ 
            $('#btnAgregarNuevoStock').html(txtbackup); 
            $('#btnAgregarNuevoStock').removeClass('disabled') 
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )


}

function DibujarNuevoStock(DatosStock = {
    'Fecha_asignacion'      :   '19-01-2024',
    'Descripcion'           :   'brotherdr-2340',
    'Cantidad'              :   '1',
    'Precio_Unitario'       :   '50980',
    'Precio_total'          :   '100000',
    'IdEmpresa'             :   '1',
    'idMarca'               :   '1',
    'tipo'                  :   'Original',
    'estado_stock'          :   'En Stock',
}, Permisos = []){
 
    var table       = $('#tbListadoStock').DataTable();

    var Fecha_asignacion        =   DatosStock.Fecha_asignacion;
    var Descripcion             =   DatosStock.Descripcion;
    var Cantidad                =   DatosStock.Cantidad;
    var Precio_Unitario         =   DatosStock.Precio_Unitario;
    var Precio_total            =   DatosStock.Precio_total;
    var IdEmpresa               =   DatosStock.IdEmpresa;
    var idMarca                 =   DatosStock.idMarca;
    var tipo                    =   DatosStock.tipo;
    var estado_stock            =   DatosStock.estado_stock;

    var rowNode     = table.row.add( [ Fecha_asignacion, Descripcion, Cantidad, Precio_Unitario, Precio_total, IdEmpresa, idMarca, tipo, estado_stock] ).draw().node();
    
    /* AGREGAMOS LOS VALORES PK */
    debugger;
    $(rowNode).attr('data-pk',DatosStock.id_stock)
    $(rowNode).attr('data-idtock',DatosStock.id_stock) 

    /* AGREGAMOS LOS IDS QUE CORRESPONDEN */
    $(rowNode).find('td:eq(0)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(1)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(2)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(3)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(4)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(5)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(6)').attr('data-pk',DatosStock.id_stock)
    $(rowNode).find('td:eq(7)').attr('data-pk',DatosStock.id_stock)

    /* INCORPORAMOS EL NOMBRE DE LA PROPIEDAD A LA TABLA */
    $(rowNode).find('td:eq(0)').attr('data-property-name','Fecha_asignacion')
    $(rowNode).find('td:eq(1)').attr('data-property-name','Descripcion')
    $(rowNode).find('td:eq(2)').attr('data-property-name','Cantidad')
    $(rowNode).find('td:eq(3)').attr('data-property-name','Precio_Unitario')
    $(rowNode).find('td:eq(4)').attr('data-property-name','Precio_total')
    $(rowNode).find('td:eq(5)').attr('data-property-name','IdEmpresa')
    $(rowNode).find('td:eq(6)').attr('data-property-name','idMarca')
    $(rowNode).find('td:eq(7)').attr('data-property-name','tipo')
    $(rowNode).find('td:eq(8)').attr('data-property-name','estado_stock') 

}

function validarInputsStock(frmkey){
    
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
        `${frmkey}-Fecha_asignacion`, /* facilitamos el ID del input que debemos validar */
        function(x){ /* -- Llamamos una función anonima con la logica que se debe cumplir */

            /* validamos el input ingresado */
            x = validarTextoInput(x)

            $(`${frmkey}-Fecha_asignacion`).val(x)

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