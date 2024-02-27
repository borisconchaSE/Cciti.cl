$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 750); 

});

// FORM functions
// ***********************************************************************************************
//
function frmNuevoDestino_Onload(){

}


// VALIDATION AND CORRECTION
// ***********************************************************************************************
//
// Funciones de validación y corrección de datos
//
function ValidarNuevoDestino(data) {

    if (data.IdTipoDestino * 1 <= 0) {
        return "Debe seleccionar un tipo de destino";
    }

    if (data.Descripcion == '') {
        return "Debe especificar el nombre del destino";
    }

    if (data.Enlace == '') {
        return "Debe especificar el enlace asignado a este destino";
    }

    return '';
}



// DISPLAY
// ***********************************************************************************************
//
// Las funciones GetDescription se llaman al pintar el campo TEXT de los options de un select
// siempre que se hayan declarado como JSDescriptionFunction en la creación del objeto
// El parametro "elemento" contiene el objeto OPTION seleccionado (valor y descripcion) y todos sus atributos "data"
//
// Las funciones deben retornar el texto que se desplegará como TEXT del OPTION
//
function LimpiarFormulario() {

    $('#frmNuevoDestino-IdTipoDestino').val('0');
    $('#frmNuevoDestino-IdTipoDestino').trigger('change');
    $('#frmNuevoDestino-Descripcion').val('');
    $('#frmNuevoDestino-Enlace').val('');

}


// EVENTS
// ***********************************************************************************************
// Obs: los eventos genericos pasan el parametro "eventInfo" con los siguientes campos:
//          - Element       : el elemento generador del evento
//          - FormKey       : el ID html del form al cual pertenece el elemento
//          - FormData      : es un objeto con la informacion de valores y atributos 'data' de los inputs que pertenecen al formulario
//          - FormElements  : es un objeto que contiene todos los objetos del formulario, para poder ser modificados
//          - Event         : si el gatillador provee este campo, se enviará el objeto 'event' completo a la función
//          - KeyCode       : para los eventos 'keyup' y 'keydown' se enviará en este parametro el campo event.keyCode (también viene en el objeto 'event')
//
function BtnGuardar_OnClick(eventInfo){

    var validacion = ValidarNuevoDestino(eventInfo.FormData);

    // Validar el formulario
    //
    if (validacion != "") {
        
        Toast.fire({
            icon: 'warning',
            title: validacion
        });

        return;
    }

    // Guardar el Destino
    //
    const equipoSvc = new DestinoSvc();

    equipoSvc.GuardarNuevo(
        {
            idTipoDestino   : JSON.stringify(eventInfo.FormData.IdTipoDestino),
            nombre          : JSON.stringify(eventInfo.FormData.Descripcion),
            enlace          : JSON.stringify(eventInfo.FormData.Enlace)
        },
        function(result){

            if($('#conpreview-new-team')){
                $('#conpreview-new-team').html(result);
            }
            
            LimpiarFormulario();

            Swal.fire({
                title: Localize("LblAtencion"),
                text: 'El destino ha sido guardado con éxito',
                showCancelButton: false,
                icon: "info",
                confirmButtonText: "OK",
                closeOnConfirm: true
            });

        },
        function(ErrorCode,ErrorMessage){  

            Swal.fire({
                title: Localize("LblAtencion"),
                text: ErrorMessage,
                showCancelButton: false,
                icon: "error",
                confirmButtonText: "OK",
                closeOnConfirm: true
            });
            
        }
    );

}