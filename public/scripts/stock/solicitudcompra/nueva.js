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
function frmCrearSolicitud_IdVersionColor_OnChange(eventInfo) {

    // Ver si se debe desbloquear el "Especificar Color"
    if (eventInfo.FormData.IdVersionColor == -2) {
        $('#frmCrearSolicitud-NombreColor').prop('disabled', false);
    }
    else {
        $('#frmCrearSolicitud-NombreColor').prop('disabled', true);
        $('#frmCrearSolicitud-NombreColor').val('');
    }
}

function AgregarError(errores, dato, condicion, mensaje) {

    if (!dato || dato == null || dato == condicion) {
        errores.push(mensaje);
    }

    return errores;
}

function Validar(eventInfo) {

    var errores = [];
    var d = eventInfo.FormData;    

    if (d.IdVersionColor*1 == -2 && d.NombreColor == '') {
        errores.push('Debe especificar el Color');
    }

    errores = AgregarError(errores, d.IdCliente, -1, 'Debe especificar el <strong>Cliente</strong>');
    errores = AgregarError(errores, d.IdCiudad, -1, 'Debe especificar la <strong>Ciudad</strong>');
    errores = AgregarError(errores, d.IdUbicacion, -1, 'Debe especificar la <strong>Ubicación</strong>');
    errores = AgregarError(errores, d.IdVehiculoNuevo, -1, 'Debe especificar el <strong>Código Interno</strong>');
    errores = AgregarError(errores, d.IdMarca, -1, 'Debe especificar la <strong>Marca</strong>');
    errores = AgregarError(errores, d.IdModelo, -1, 'Debe especificar el <strong>Modelo</strong>');
    errores = AgregarError(errores, d.IdVersion, -1, 'Debe especificar la <strong>Versión</strong>');
    errores = AgregarError(errores, d.IdVersionColor, -1, 'Debe especificar el <strong>Color</strong>');
    errores = AgregarError(errores, d.Agno, -1, 'Debe especificar el <strong>Año</strong> del Modelo');

    if (errores.length == 0) {
        return true;
    }

    DisplayErrors(errores, 'error', 'Atención!', 'OK', '#DD6B55', true);

    return false;
}

function CorregirValores(data) {

    if (data.IdVersionColor*1 != -2) {
        data.NombreColor = "-1";
    }

    if (data.IdMarca == '') {
        data.IdMarca = -1;
    }

    if (data.IdModelo == '') {
        data.IdModelo = -1;
    }

    if (data.IdVersion == '') {
        data.IdVersion = -1;
    }

    if (data.IdVersionColor == '') {
        data.IdVersionColor = -1;
    }

    if (data.IdUbicacion == '') {
        data.IdUbicacion = -1;
    }

    if (data.IdVehiculoNuevo == '') {
        data.IdVehiculoNuevo = -1;
    }

    return data;
}

function btnGuardarSolicitud_OnClick(eventInfo) {

    if (!Validar(eventInfo)) {
        return;
    }
 
    const solicitudCompraSvc = new SolicitudCompraSvc();

    // Datos para la nueva solicitud
    var editarSolicitudData = {
        IdSolicitudCompra       : eventInfo.FormData.IdSolicitudCompra,
        IdCliente               : eventInfo.FormData.IdCliente*1,
        IdMarca                 : eventInfo.FormData.IdMarca*1,
        IdModelo                : eventInfo.FormData.IdModelo*1,
        IdVersion               : eventInfo.FormData.IdVersion*1,
        IdVersionColor          : eventInfo.FormData.IdVersionColor*1,
        NombreColor             : eventInfo.FormData.NombreColor,
        IdCiudad                : eventInfo.FormData.IdCiudad,
        IdUbicacion             : eventInfo.FormData.IdUbicacion*1,
        IdVehiculoNuevo         : eventInfo.FormData.IdVehiculoNuevo,
        Agno                    : eventInfo.FormData.Agno*1, 
    }

    editarSolicitudData = CorregirValores (editarSolicitudData);  

    solicitudCompraSvc.ModificarSolicitud(
        editarSolicitudData,
        function (result) {

            Swal.fire({
                icon: 'success',
                title: '',
                text: 'Solicitud modificada con éxito'
            }).then((result) => {                
                JumpTo('/stock/solicitudcompra/versolicitudes');
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

function btnCrearSolicitud_OnClick(eventInfo) {

    if (!Validar(eventInfo)) {
        return;
    }

    const solicitudCompraSvc = new SolicitudCompraSvc();

    // Datos para la nueva solicitud
    var nuevaSolicitudData = {        
        IdCliente               : eventInfo.FormData.IdCliente*1,
        IdMarca                 : eventInfo.FormData.IdMarca*1,
        IdModelo                : eventInfo.FormData.IdModelo*1,
        IdVersion               : eventInfo.FormData.IdVersion*1,
        IdVersionColor          : eventInfo.FormData.IdVersionColor*1,
        NombreColor             : eventInfo.FormData.NombreColor,
        IdCiudad                : eventInfo.FormData.IdCiudad,
        IdUbicacion             : eventInfo.FormData.IdUbicacion*1,
        IdVehiculoNuevo         : eventInfo.FormData.IdVehiculoNuevo,
        Agno                    : eventInfo.FormData.Agno*1,
    }

    nuevaSolicitudData = CorregirValores (nuevaSolicitudData);

    // Intentar insertar la nueva solicitud
    solicitudCompraSvc.InsertarSolicitud(
        nuevaSolicitudData,
        function (insertResult) {
            console.log(insertResult);
            Swal.fire({
                icon: 'success',
                title: '',
                text: 'Solicitud ingresada con éxito'
            }).then((result) => {                
                JumpTo('/stock/solicitudcompra/nuevasolicitud');
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