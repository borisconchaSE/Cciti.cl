$(function() {

    // Hide navigation
    setTimeout(HideNavigation(), 250);

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

var popupAprobar = null;

function btnSolicitarAprobarSolicitud_OnClick(rowInfo) {

    var idSolicitud = rowInfo.RowData.pk;

    var service = new SolicitudCompraSvc();

    popupAprobar = NewPopUp({
        modalSize: 'lg'
    });

    service.PopUpAprobarSolicitud(
        idSolicitud,
        function(result){
            $(popupAprobar).RefreshPopUp(result);           
        },
        function(errorCode, errorMessage){
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });

            $(popupAprobar).DismissPopUp();
        }
    );
    
}

function btnAprobarSolicitud_OnClick(eventInfo) {

    if (!ValidarAprobacion(eventInfo)) {
        Toast.fire({
            icon: 'error',
            title: 'El Codigo Interno es obligatorio'
        });
        return;
    }
    else {

        // Enviar la solicitud de aprobaciony los datos adicionales al controlador para que se almacenen
        var service = new SolicitudCompraSvc();

        service.AprobarSolicitud(
            eventInfo.FormData.IdSolicitudCompra,
            eventInfo.FormData.IdVehiculoNuevo,
            function(result) {

                $(popupAprobar).DismissPopUp();

                // Quitamos el elemento de la grilla
                DropTableRow('tbSolicitudesPendientes', eventInfo.FormData.IdSolicitudCompra);

                Swal.fire({
                    title: 'Finalizado',
                    text: 'Se ha APROBADO la solicitud',
                    icon: "success",
                    confirmButtonText: "OK",
                });
            },
            function(errorCode, errorMessage){
                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
            }
        );        
    
        
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

            var IdSolicitudCompra  = eventInfo.RowData.idsolicitudcompra;        
            var solicitudCompraSvc = new SolicitudCompraSvc();
        
            solicitudCompraSvc.RechazarSolicitud(
                IdSolicitudCompra,
                function(result){

                    // Quitamos el elemento de la grilla
                    DropTableRow('tbSolicitudesPendientes', eventInfo.RowData.pk);

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