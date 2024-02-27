$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

function btnEliminarSolicitudPreEntrega_OnClick(rowInfo) {

    var idSolicitud = rowInfo.RowData.pk;

    Swal.fire({
        title: '¿Está seguro que desea ELIMINAR esta Solicitud de Pre-Entrega?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Eliminar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {
        
        if(result.isConfirmed == true) {
            var service = new PreEntregaSvc();

            service.EliminarSolicitudPreEntrega(
                idSolicitud,
                function(result) {
                    if (result.ErrorCode && result.ErrorCode != 0) {
                        Toast.fire({
                            icon: 'error',
                            title: result.Message
                        });
                    }
                    else {
                        // Quitamos el elemento de la grilla
                        DropTableRow('tbResultadoSolicitudesPreEntrega', idSolicitud);
        
                        Swal.fire({
                            title: 'Finalizado',
                            text: 'Se ha ELIMINADO la Solicitud de Pre-Entrega',
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                    }
                },
                function(errorCode, errorMessage){
                    Toast.fire({
                        icon: 'error',
                        title: errorMessage
                    });
                }
            );   
        }

    });
}

function btnBuscarSolicitudes_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-solicitudes').html($('#content-wait-modal').html());

    const preEntregaSvc = new PreEntregaSvc();

    var marca  = $('#frmBuscarSolicitudPreEntrega-IdMarca  option:selected').text(); // versiones en texto    
    var modelo = $('#frmBuscarSolicitudPreEntrega-IdModelo option:selected').text();

    if (eventInfo.FormData.IdMarca == -1) {
        marca = '-1';
    }

    if (eventInfo.FormData.IdModelo == -1) {
        modelo = '-1';
    }

    var rutCliente     = (!eventInfo.FormData.Rut || eventInfo.FormData.Rut == '')       ? '-1' : eventInfo.FormData.Rut;
    var codigoVehiculo = (!eventInfo.FormData.CodigoVehiculo || eventInfo.FormData.CodigoVehiculo == '') ? '-1' : eventInfo.FormData.CodigoVehiculo;
    var numVenta       = (!eventInfo.FormData.NumVenta || eventInfo.FormData.NumVenta == '')       ? '-1' : eventInfo.FormData.NumVenta;

    preEntregaSvc.BuscarSolicitudes(
        rutCliente, eventInfo.FormData.IdTipoAuto, 
        codigoVehiculo, numVenta,
        eventInfo.FormData.FechaDesde, eventInfo.FormData.FechaHasta,
        eventInfo.FormData.IdAreaPreEntrega,
        marca, modelo, eventInfo.FormData.IdTipoEstadoSolicitudPreEntrega,
        function(result){
            $('#container-resultados-solicitudes').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-solicitudes').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );

}