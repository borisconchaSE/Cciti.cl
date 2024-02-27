$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

function btnBuscarPreEntregasTerminadas_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-pre-entregas-terminadas').html($('#content-wait-modal').html());

    const preEntregaSvc = new PreEntregaSvc();

    var marca  = $('#frmBuscarPreEntregas-IdMarca  option:selected').text(); // versiones en texto    
    var modelo = $('#frmBuscarPreEntregas-IdModelo option:selected').text();

    if (eventInfo.FormData.IdMarca == -1) {
        marca = '-1';
    }

    if (eventInfo.FormData.IdModelo == -1) {
        modelo = '-1';
    }

    preEntregaSvc.BuscarPreEntregasTerminadas(
        eventInfo.FormData.FechaDesde, eventInfo.FormData.FechaHasta,
        marca, modelo,
        eventInfo.FormData.IdAreaPreEntrega,
        function(result){
            $('#container-resultados-pre-entregas-terminadas').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-pre-entregas-terminadas').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );

}
function btnRechazarCheckListPreEntregaTerminada_OnClick(rowInfo) {

    var idPreEntrega = rowInfo.RowData.pk;

    Swal.fire({
        title: '¿Está seguro que desea Rechazar esta Pre-Entrega?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Rechazar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){
        
            var service = new PreEntregaSvc();
            
            service.RechazarSolicitudPreEntregaTerminada(
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
                        DropTableRow('tbResultadoPreEntregaTerminada', idPreEntrega);
        
                        Swal.fire({
                            title: 'Finalizado',
                            text: 'Se ha Rechazado la Pre-Entrega. La siguiente Fecha es ' + result.Fecha + ' a las ' + result.Hora + ':00 horas',
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
function btnAprobarPreEntregaTerminada_OnClick(rowInfo) {

    var idPreEntrega = rowInfo.RowData.pk;

    Swal.fire({
        title: '¿Está seguro que desea Aprobar esta Pre-Entrega?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Aprobar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){
        
            var service = new PreEntregaSvc();
            
            service.FinalizarPreEntregaTerminada(
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
                        DropTableRow('tbResultadoPreEntregaTerminada', idPreEntrega);
        
                        Swal.fire({
                            title: 'Aprobado',
                            text: 'Se ha Aprobado la Pre-Entrega. ',
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