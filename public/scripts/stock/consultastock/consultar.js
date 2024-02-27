$(function() {

    // Hide navigation
    setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

function TabUsadosLoad(pageInfo) {

    return "/stock/consultastock/consultarusados";

}

function TabNuevosLoad(pageInfo) {

    return "/stock/consultastock/consultarnuevos";

}



function btnBuscarUsados_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-usados').html($('#content-wait-modal').html());

    const consultaStockSvc = new ConsultaStockSvc();

    consultaStockSvc.BuscarUsados(
        eventInfo.FormData.PeriodoDesde, eventInfo.FormData.PeriodoHasta,
        eventInfo.FormData.KilometrajeDesde, eventInfo.FormData.KilometrajeHasta,
        eventInfo.FormData.PrecioDesde, eventInfo.FormData.PrecioHasta,
        eventInfo.FormData.IdVehiculo, eventInfo.FormData.IdMarca,
        eventInfo.FormData.IdModelo, eventInfo.FormData.IdColor,
        eventInfo.FormData.IdTransmision, eventInfo.FormData.IdTraccion,
        eventInfo.FormData.IdCombustible, eventInfo.FormData.IdEstadoCompra, eventInfo.FormData.IdEstadoVenta,
        function(result){
            $('#container-resultados-usados').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-usados').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );

}
function btnBuscarNuevos_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-nuevos').html($('#content-wait-modal').html());

    const consultaStockSvc = new ConsultaStockSvc();

    consultaStockSvc.BuscarNuevos(
        eventInfo.FormData.PeriodoDesde, 
        // eventInfo.FormData.PeriodoHasta,
        // eventInfo.FormData.PrecioDesde, eventInfo.FormData.PrecioHasta,
        eventInfo.FormData.IdEstadoCompra, eventInfo.FormData.IdEstadoVenta,
        eventInfo.FormData.IdVehiculo, eventInfo.FormData.IdMarca,
        eventInfo.FormData.IdModelo, 
        eventInfo.FormData.IdVersion,
        eventInfo.FormData.IdColor,
        eventInfo.FormData.IdUbicacion,
        function(result){
            $('#container-resultados-nuevos').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-nuevos').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );

}

var popupPreEntrega = null;

function btnNuevaSolicitudPreEntregaNuevos_OnClick(rowInfo) {

    // abrir un popup con la información
    var nVenta          = rowInfo.RowData.codigointernovehiculo;
    var idVehiculo      = rowInfo.RowData.idvehiculonuevo;

    const service = new PreEntregaSvc();

    var popupPreEntrega = NewPopUp({
        modalSize: 'md'
    });

    service.PopUpPreEntrega(
        nVenta, 'NUEVOS', idVehiculo,
        function(result){
            $(popupPreEntrega).RefreshPopUp(result);           
        },
        function(errorCode, errorMessage){
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
            $('.modal').modal('hide');
        }
    );
}

function btnNuevaSolicitudPreEntregaUsados_OnClick(rowInfo) {

    // abrir un popup con la información
    var nVenta          = rowInfo.RowData.nventa;
    var idVehiculo      = rowInfo.RowData.pk;

    const service = new PreEntregaSvc();

    var popupPreEntrega = NewPopUp({
        modalSize: 'md'
    });

    service.PopUpPreEntrega(
        nVenta, 'USADOS', idVehiculo,
        function(result){
            $(popupPreEntrega).RefreshPopUp(result);           
        },
        function(errorCode, errorMessage){
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
            $('.modal').modal('hide');
        }
    );
}

function btnVerDetalleVehiculoUsado_OnClick(rowInfo) {

    // abrir un popup con la información
    var idVehiculoUsado = JSON.stringify(rowInfo.RowData.pk)
    

    const service = new ConsultaStockSvc();

    var popup = NewPopUp({
        modalSize: 'xl'
    });

    service.PopUpVehiculoUsado(
        idVehiculoUsado,
        function(result){
            $(popup).RefreshPopUp(result);           
        },
        function(errorCode, errorMessage){
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
            $('.modal').modal('hide');
        }
    );
}

function btnVerDetalleVehiculoNuevo_OnClick(rowInfo) {

    // abrir un popup con la información
    var idVehiculoNuevo = rowInfo.RowData.pk

    const service = new ConsultaStockSvc();

    var popup = NewPopUp({
        modalSize: 'xl'
    });

    service.PopUpVehiculoNuevo(
        idVehiculoNuevo,
        function(result){
            $(popup).RefreshPopUp(result);           
        },
        function(errorCode, errorMessage){
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
            $('.modal').modal('hide');
        }
    );
}


// Usados
function btnReset_OnClick(eventInfo){

    
    $('#frmBuscarUsados-KilometrajeDesde').val(-1).trigger("change");
    $('#frmBuscarUsados-KilometrajeHasta').val(-1).trigger("change");
    $('#frmBuscarUsados-PeriodoDesde').val(-1).trigger("change");
    $('#frmBuscarUsados-PeriodoHasta').val(-1).trigger("change");
    $('#frmBuscarUsados-PrecioDesde').val(-1).trigger("change");
    $('#frmBuscarUsados-PrecioHasta').val(-1).trigger("change");
    $('#frmBuscarUsados-IdVehiculo').val(-1).trigger("change");
    $('#frmBuscarUsados-IdMarca').val(-1).trigger("change");
    $('#frmBuscarUsados-IdModelo').val(-1).trigger("change");
    $('#frmBuscarUsados-IdEstadoCompra').val(-1).trigger("change");
    $('#frmBuscarUsados-IdEstadoVenta').val(-1).trigger("change");
    $('#frmBuscarUsados-IdColor').val(-1).trigger("change");
    $('#frmBuscarUsados-IdTransmision').val(-1).trigger("change");
    $('#frmBuscarUsados-IdTraccion').val(-1).trigger("change");
    $('#frmBuscarUsados-IdCombustible').val(-1).trigger("change");
    
}
   
// Nuevos
function btnLimpiar_OnClick(eventInfo){
    
    $('#frmBuscarNuevos-PeriodoDesde').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdEstadoCompra').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdEstadoVenta').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdVehiculo').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdMarca').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdModelo').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdColor').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdCiudad').val(-1).trigger("change");
    $('#frmBuscarNuevos-IdUbicacion').val(-1).trigger("change");
    //$('#frmBuscarNuevos-IdBodega').val(-1).trigger("change");

}