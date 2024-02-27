$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 250);

});

// FORM functions
// ***********************************************************************************************
//

function frmNuevaSolicituPreEntrega_IdAreaPreEntrega_OnChange(eventInfo) {

    var idAreaPreEntrega = eventInfo.FormData.IdAreaPreEntrega *1;   

    if (idAreaPreEntrega >= 0) {
        // llamar al servicio y obtener la siguiente fecha
        const preEntregaSvc = new PreEntregaSvc();

        preEntregaSvc.ObtenerSiguienteHoraAgendamiento(
            idAreaPreEntrega,
            function(result){

                // Ver si obtubimos fecha
                var labelStyle = 'label-default';

                if (result.IdAreaPreEntregaHorario < 0) {
                    labelStyle = 'label-danger';
                }
                else {
                    labelStyle = 'label-success-alt';
                }

                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-default');
                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-danger');
                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-success-alt');

                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').addClass(labelStyle);

                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').html(result.Fecha);
                $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario').val(result.IdAreaPreEntregaHorario);
                $('#FechaAgenda').val(result.FechaAgenda);
            },
            function(ErrorCode,ErrorMessage){

                Toast.fire({
                    icon: 'error',
                    title: ErrorMessage
                });
            }
        );        
    }
    else {
        
        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-default');
        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-danger');
        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').removeClass('label-success-alt');
        
        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').addClass('label-default');

        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario').val(-1);
        $('#frmNuevaSolicituPreEntrega-IdAreaPreEntregaHorario-content').html("<< Seleccione Area");
        $('#FechaAgenda').val('');
    }

}

function getLi(text) {
    return "<li style='text-align: left;'>" + text + "</li>";
}

function ValidarNuevaSolicitud(data) {

    var result = true;

    var mensaje = "<ul>";

    // Validar que tengamos horario disponible
    if (!data.IdAreaPreEntregaHorario || data.IdAreaPreEntregaHorario <= 0) {
        mensaje += getLi("Debe seleccionar un horario disponible");
        result = false;
    }

    if (!data.IdNotaVenta || data.IdNotaVenta <= 0) {
        mensaje += getLi("Debe seleccionar una nota de venta");
        result = false;
    }

    mensaje += "</ul>";

    if (!result) {
        Swal.fire({
            icon: 'error',
            title: 'Atenciòn!',
            html: mensaje
        });
    }

    return result;

}

function btnGuardarNuevaSolicitud_OnClick(eventInfo) {

    if (!ValidarNuevaSolicitud(eventInfo.FormData)) {
        return;
    }

    var pk = eventInfo.FormData.IdVehiculo;
    const preEntregaSvc = new PreEntregaSvc();

    preEntregaSvc.GuardarNuevaSolicitud(
        eventInfo.FormData.FechaAgenda,
        eventInfo.FormData.IdAreaPreEntrega,
        eventInfo.FormData.TipoAuto,
        eventInfo.FormData.IdNotaVenta,
        eventInfo.FormData.IdAreaPreEntregaHorario,
        function(result){

            Swal.fire({
                icon: 'success',
                title: '',
                text: 'Solicitud ingresada con éxito'
            }).then((result) => {       
                
                $('.modal').modal('hide');
                
                //JumpTo('/stock/preentrega/consultarsolicitudes');
            });
        },
        function(ErrorCode,ErrorMessage){
            Toast.fire({
                icon: 'error',
                title: ErrorMessage
            });
        }
    );

}