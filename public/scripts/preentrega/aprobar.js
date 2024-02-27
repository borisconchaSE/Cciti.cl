$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//
function btnBuscarSolicitudesAprobar_OnClick(eventInfo) {

    $('#container-resultados-solicitudes').html($('#content-wait-modal').html());

    const preEntregaSvc = new PreEntregaSvc();

    preEntregaSvc.BuscarSolicitudesAprobar(
        eventInfo.FormData.FechaDesde, 
        eventInfo.FormData.FechaHasta,
        eventInfo.FormData.IdAreaPreEntrega,
        function(result){
            $('#container-resultados-solicitudes').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-solicitudes').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );
}

var popupAprobar = null;

function btnSolicitarAprobarSolicitud_OnClick(rowInfo) {

    var idSolicicitud = rowInfo.RowData.pk;

    if (!ValidarAprobacion(rowInfo)) {
        Toast.fire({
            icon: 'error',
            title: 'Datos no válidos'
        });
        return;
    }
    else {

        Swal.fire({
            title: '¿Está seguro que desea APROBAR esta Solicitud?', 
            showCancelButton: true,
            icon: "question",
            confirmButtonText: "Sí, Aprobar",
            cancelButtonText: 'Cancelar', 
        }).then((result) => {    
    
            if(result.isConfirmed == true){
          
                var service = new PreEntregaSvc();

                service.AprobarSolicitud(
                    rowInfo.RowData.pk,
                    function(result) {

                        if (result.ErrorCode && result.ErrorCode != 0) {
                            Toast.fire({
                                icon: 'error',
                                title: result.Message
                            });
                        }
                        else {
                            // Quitamos el elemento de la grilla
                            DropTableRow('tbSolicitudesPreEntrega', idSolicicitud);
            
                            Swal.fire({
                                title: 'Finalizado',
                                text: 'Se ha APROBADO la solicitud',
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

function btnAprobarSolicitud_OnClick(eventInfo) {




        // Enviar la solicitud de aprobaciony los datos adicionales al controlador para que se almacenen
           
    
        
    }
}

function ValidarAprobacion(eventInfo) {

    return true;
}

function btnRechazarSolicitud_OnClick(eventInfo) {

    Swal.fire({
        title: '¿Está seguro que desea RECHAZAR esta Solicitud?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Rechazar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){

            var IdSolicitudPreEntega  = eventInfo.RowData.idsolicitudpreentrega;        
            var servicio = new PreEntregaSvc();
        
            servicio.RechazarSolicitud(
                IdSolicitudPreEntega,
                function(result){

                    // Quitamos el elemento de la grilla
                    DropTableRow('tbSolicitudesPreEntrega', eventInfo.RowData.pk);

                    Swal.fire({
                        title: 'Finalizado',
                        text: 'Se ha RECHAZADO la solicitud',
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