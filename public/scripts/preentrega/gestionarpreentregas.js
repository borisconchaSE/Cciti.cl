$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

function btnBuscarPreEntregas_OnClick(eventInfo) {

    // mostrar la animation de espera...
    $('#container-resultados-pre-entregas').html($('#content-wait-modal').html());

    const preEntregaSvc = new PreEntregaSvc();

    var marca  = $('#frmBuscarPreEntregas-IdMarca  option:selected').text(); // versiones en texto    
    var modelo = $('#frmBuscarPreEntregas-IdModelo option:selected').text();

    if (eventInfo.FormData.IdMarca == -1) {
        marca = '-1';
    }

    if (eventInfo.FormData.IdModelo == -1) {
        modelo = '-1';
    }

    preEntregaSvc.BuscarPreEntregas(
        eventInfo.FormData.FechaDesde, eventInfo.FormData.FechaHasta,
        marca, modelo,
        eventInfo.FormData.IdAreaPreEntrega, eventInfo.FormData.IdTipoEstadoPreEntrega,
        function(result){
            $('#container-resultados-pre-entregas').html(result);
        },
        function(ErrorCode,ErrorMessage){
            $('#container-resultados-pre-entregas').html('<div class="note-box">' + ErrorMessage + '</div>')    
        }
    );

}

var popupCheckList = null;


function btnGestionarCheckListPreEntrega_OnClick(rowInfo) {

    var idPreEntrega = JSON.stringify(rowInfo.RowData.pk)

    const service = new PreEntregaSvc();

    var popup = NewPopUp({
        modalSize: 'md'
    });

    service.PopUpCheckList(
        idPreEntrega,
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

function btnFinalizarPreEntrega_OnClick(rowInfo) {

    var idPreEntrega = rowInfo.RowData.pk;

    Swal.fire({
        title: '¿Está seguro que desea FINALIZAR esta Pre-Entrega?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Finalizar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){
        
            var service = new PreEntregaSvc();

            service.FinalizarPreEntrega(
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
                        DropTableRow('tbResultadoPreEntrega', idPreEntrega);
        
                        Swal.fire({
                            title: 'Finalizado',
                            text: 'Se ha FINALIZADO la Pre-Entrega',
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