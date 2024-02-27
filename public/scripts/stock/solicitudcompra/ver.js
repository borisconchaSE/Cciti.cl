$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

// function TabUsadosLoad(pageInfo) {

//     return "/stock/consultastock/consultarusados";

// }

// function TabNuevosLoad(pageInfo) {

//     return "/stock/consultastock/consultarnuevos";

// }


function btnBuscarSolicitud_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-solicitudes').html($('#content-wait-modal').html());

    const solicitudCompraSvc = new SolicitudCompraSvc();

    solicitudCompraSvc.BuscarSolicitudes(
        eventInfo.FormData.PeriodoDesde,
        eventInfo.FormData.CodigoCliente,
        eventInfo.FormData.IdMarca,
        eventInfo.FormData.IdModelo,
        eventInfo.FormData.IdVersion,
        eventInfo.FormData.IdColor,
        eventInfo.FormData.IdUbicacion,
        eventInfo.FormData.IdTipoEstadoSolicitud,
        eventInfo.FormData.IdVehiculoNuevo,
        function(result){
            $('#container-resultados-solicitudes').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-solicitudes').html('<div class="note-box">' + ErrorMessage + '</div>')
        }
    );

}

function btnEliminarSolicitud_OnClick(eventInfo) {

    Swal.fire({
        title: '¿Está seguro que desea eliminar este item?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Eliminar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){

            var IdSolicitudCompra  = eventInfo.RowData.idsolicitudcompra;        
            var solicitudCompraSvc = new SolicitudCompraSvc();
        
            solicitudCompraSvc.EliminarSolicitud(
                IdSolicitudCompra,
                function(result){

                    // Quitamos el elemento de la grilla
                    DropTableRow('tbResultadoSolicitudes', eventInfo.RowData.pk);

                    Swal.fire({
                        title: 'Finalizado',
                        text: 'Se ha eliminado la solicitud',
                        icon: "success",
                        confirmButtonText: "OK",
                    })   
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


function btnEditarSolicitud_OnClick(rowInfo) {

    var idSolicitudCompra  = rowInfo.RowData.idsolicitudcompra;
    JumpTo('/stock/solicitudcompra/editarsolicitud', { "IdSolicitudCompra" : idSolicitudCompra});

}


function btnVerDetalleVehiculoNuevo_OnClick(rowInfo) {

    // abrir un popup con la información
    var idVehiculoNuevo = rowInfo.RowData.pk

    const service = new ConsultaSolicitudSvc();

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



function btnLimpiar_OnClick(eventInfo){

    $('#frmBuscarSolicitudes-CodigoCliente').val('').trigger("change");
    $('#frmBuscarSolicitudes-PeriodoDesde').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdVehiculo').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdMarca').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdModelo').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdVersion').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdColor').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdCiudad').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdUbicacion').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdVehiculoNuevo').val(-1).trigger("change");
    $('#frmBuscarSolicitudes-IdTipoEstadoSolicitud').val(-1).trigger("change");

}