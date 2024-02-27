$(function() {

    // Hide navigation
    //setTimeout(HideNavigation(), 750); 

});

// FORM functions
// ***********************************************************************************************
//
function frmencabezado_Onload(){

}

function buscarDestinos (tipoDestino, estado, descripcion) {

    $('#formulario-destinos-contenido').html($('#content-wait-modal').html());

    const destinoSvc = new DestinoSvc();

    if (descripcion == '') {
        descripcion = '---';
    }

    destinoSvc.Buscar(
        {
            idTipoDestino               : JSON.stringify(tipoDestino),
            idTipoEstadoDestino         : JSON.stringify(estado),
            nombre                      : JSON.stringify(descripcion)
        },
        function(result){
            $('#formulario-destinos-contenido').html(result);
        },
        function(ErrorCode, ErrorMessage){
            $('#formulario-destinos-contenido').html('')    
        }
    );
}

// VALIDATION AND CORRECTION
// ***********************************************************************************************
//
// Funciones de validación y corrección de datos
//




// DISPLAY
// ***********************************************************************************************
//
// Las funciones GetDescription se llaman al pintar el campo TEXT de los options de un select
// siempre que se hayan declarado como JSDescriptionFunction en la creación del objeto
// El parametro "elemento" contiene el objeto OPTION seleccionado (valor y descripcion) y todos sus atributos "data"
//
// Las funciones deben retornar el texto que se desplegará como TEXT del OPTION
//



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
function BtnBuscar_OnClick(eventInfo){

    var tipoDestino   = eventInfo.FormData.IdTipoDestino;
    var estado        = eventInfo.FormData.IdTipoEstadoDestino;
    var descripcion   = eventInfo.FormData.Descripcion;

    buscarEquipos(tipoDestino, estado, descripcion);
}

function PopUpOpcionesPDF() {

    const Svc = new DestinoSvc();
    const popupSettingsPDf = NewPopUp({
        dismissOnOutsideClick : true
    });


    Svc.PopUpOpcionesPDF(
        null,
        function(content){
            $(popupSettingsPDf).RefreshPopUp(content);
            
        },
        function(errorCode,errorMessage){

            Swal.fire({
                icon: 'error', 
                text: errorMessage, 
              })

        }
    )    

}


function _checkTeamList(input){
    if(window.selectedTeams == null){
        window.selectedTeams = new Array();
    }
    var value = input.value;
    setTimeout(() => {
        if( $(input).is(":checked") ){
            if( window.selectedTeams.includes(value) == false ){
                window.selectedTeams.push(value);
            } 
        }else{
            if( window.selectedTeams.includes(value) != false ){
                let position = window.selectedTeams.indexOf(value);
                window.selectedTeams.splice(position, 1);
            }  
        }
    }, 50);
}


function btnGenerarPdf_OnClick(eventinfo) { 
    var inputs              = $('.pdf-method-export:radio:checked');
    var selectedOptions     = (inputs.val() > 0) ? inputs.val() : 1;
    new qrPDF(
         window.selectedTeams,
         selectedOptions
    );  
}

function BtnEditar_OnClick(eventinfo){

    window.Button           =  eventinfo.Element;
    window.TrElement        =  $(eventinfo.Element).parents("tr:first")[0];
    window.TableElement     =  $(window.TrElement ).parents("table:first")[0];
    
    const Svc = new DestinoSvc();
    
    const popupEditarDestino = NewPopUp({
        dismissOnOutsideClick : true
    });

    var entity = {
        idDestino : JSON.stringify(eventinfo.RowData.iddestino)
    } 

    Svc.PopUpEditarDestino(
        entity,
        function(content){ 
            popupEditarDestino.RefreshPopUp(content);  
        },
        function(errorCode, errorMessage){

            Swal.fire({
                icon: 'error', 
                text: errorMessage, 
            })

        }
    );
    

}

function btnGuardarCambiosEditDestino_OnClick(eventinfo){
 

    var entity = {
        IdDestino                   : eventinfo.FormData.IdDestino,
        Descripcion                 : eventinfo.FormData.Descripcion,
        Enlace                      : eventinfo.FormData.Enlace,
        IdTipoDestino               : eventinfo.FormData.IdTipoDestino,
        IdTipoEstadoDestino         : eventinfo.FormData.IdTipoEstadoDestino
    };

    var regex = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    var status = new RegExp(regex);
    
    if(status.test(entity.Enlace) != true){
        Swal.fire({
            icon: 'error', 
            text: 'Debe ingresar un enlace valido', 
        })
        return;
    }
    if(entity.Descripcion.length < 1 || entity.Descripcion == ""){
        Swal.fire({
            icon: 'error', 
            text: 'Debe ingresar un nombre de destino', 
        })
        return;
    }

    // guardamos los cambios

    const service = new DestinoSvc();

    service.GuardarCambiosDestino(
        {
            Destino : JSON.stringify(entity),
            tbkey   : 'tbListaDestinos'
        },
        function(success){

            UpdateCellDataTable(entity.Descripcion,['Descripcion']);
            var  PropertyName = getInputTextSelect2('IdTipoDestino',eventinfo);

            UpdateCellDataTable(PropertyName ,['Clasificacion']);
            UpdateCellDataTable(entity.Enlace,['Enlace']);

            if(entity.IdTipoEstadoDestino == 1){
                UpdateCellDataTable('<span class="label label-success md" id="">ACTIVO</span>' ,['Estado']);
            }else{
                UpdateCellDataTable('<span class="label label-danger md" id="">INACTIVO</span>' ,['Estado']);
            }

            Swal.fire({
                icon: 'success', 
                text: 'Cambios guardados correctamente', 
            });
            $('.modal').modal('hide'); 
        },
        function(errorCode,errorMessage){
            Swal.fire({
                icon: 'error', 
                text: errorMessage, 
            })
            
        }
    );

}

function BtnVerQr_OnClick( eventinfo ) {
    
    var entity = {
        IdDestino : JSON.stringify(eventinfo.RowData.iddestino)
    }

    const popupVerCodigoQR = NewPopUp({
        dismissOnOutsideClick : true
    });

    const service = new DestinoSvc();

    service.VerCodigoQR(
        entity,
        function(content){
            popupVerCodigoQR.RefreshPopUp(content);  
        },
        function(errorCode,errorMessage){
            
            $('.modal').modal('hide'); 
            
            Swal.fire({
                icon: 'error', 
                text: errorMessage, 
            })
      
        }
    )

}

function BtnEliminarDestino_OnClick(eventinfo) {
     
    TrElement        =  $(eventinfo.Element).parents("tr:first")[0];
    TableElement     =  $(TrElement ).parents("table:first")[0];

    var entity = {
        idDestino   : JSON.stringify(eventinfo.RowData.iddestino),
        tbkey       : JSON.stringify(TableElement.id)
    };

    const service = new DestinoSvc();

    Swal.fire({
        title: '¿Está seguro que desea eliminar el destino?', 
        showCancelButton: true,
        icon: "question",
        confirmButtonText: "Sí, Confirmar",
        cancelButtonText: 'Cancelar', 
    }).then((result) => {    

        if(result.isConfirmed == true){            

            const tableitem = $(`#${TableElement.id}`).DataTable();
      
            service.EliminarCodigoQR(
                entity,
                function(success){

                    Swal.fire({
                        icon: 'success', 
                        text: 'Destino eliminado con éxito', 
                    });

                    tableitem.ajax.reload();
                },
                function(errorCode,errorMessage){

                    Swal.fire({
                        icon: 'error', 
                        text: errorMessage, 
                    })
        
                }
            );

        }

    });

}


function btnNuevaCargaMasivaExcel_OnClick(eventinfo) {

    $( '#carga-masiva-preview' ).html('');

    var fileUpload = $("#frmCargaMasivaQR-fileInput").get(0);
    var files = fileUpload.files;

    if(files.length < 1){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Debe seleccionar un archivo a cargar', 
          })
          return;

    }
    console.log('archivo detectado')

    var fileData = new FormData();
    fileData.append('NuevoDestinoMasivo', files[0]);

    Swal.fire({
        title: 'Procesando...',
        html: 'Estamos validando y cargando su archivo... esto puede tardar unos segundos', 
        didOpen: () => {
          Swal.showLoading()
          
        }
    });

    const service = new DestinoSvc();

    service.ValidarCargaMasiva(
        fileData,
        function(success){
            Swal.fire({
                icon: 'success', 
                text: 'Carga masiva generada con exito', 
            });
            $( '#carga-masiva-preview' ).html(success);          
        },
        function(errorCode,errorMessage){
            Swal.fire({
                icon: 'error', 
                text: errorMessage, 
            })

        }
    );

}

