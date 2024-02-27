function HideNavigation() {

    if (!($("body").hasClass('hide-sidebar'))) {
        $("body").toggleClass("hide-sidebar");
    }    

    /*
    $('.hide-menu').on('click', function(event){
        event.preventDefault();
        if ($(window).width() < 769) {
            $("body").toggleClass("show-sidebar");
        } else {
            $("body").toggleClass("hide-sidebar");
        }
    });
    */

}

function ShowNavigation() {

    if ($("body").hasClass('hide-sidebar')) {
        $("body").toggleClass("show-sidebar");
    }

}

function CheckSessionResult(result) {

    if (result.ErrorCode == ErrorCodeEnum.ERR_NO_SESSION) {

        Swal.fire({
            title: Localize('ErrNoSessionTitle'),
            text: Localize('ErrNoSessionMessage'),
            showCancelButton: false,
            icon: "warning",
            confirmButtonText: "OK",
            closeOnConfirm: false
        }, function () {                                                                
            document.location.href = '/core/login';
        });

        return false;
    }
    else {
        return true;
    }
}

function StartDownload(url, params) {

    var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr({
        action: url
    }).appendTo(document.body);

    for (var i in params) {
        if (params.hasOwnProperty(i)) {
            $('<input type="hidden" />').attr({
                name: i,
                value: params[i]
            }).appendTo(f);
        }
    }
    
    f.submit();    
    f.remove();

}


function openUrlModal(title, description, contentUrl, color, useHttps = true) {
    $('#dynModal').find('#modal-body').html('Por favor espere...');
    $('#dynModal').find('#modal-title').html(title);
    $('#dynModal').find('#modal-description').html(description);

    $('#dynModal').find('#modal-body').html($('#content-wait.modal').html());
    $('#dynModal').modal();

    $.ajax({
        type     : 'get',
        contentType: "application/json; charset=UTF-8",
        dataType : 'json',
        url      : contentUrl,
        success: function(data) {
            if (data.ErrorCode > 0) {
                $('#dynModal').find('#modal-body').html(data.ErrorMessage);
                //$('#dynModal').modal();
            }
            else {
                $('#dynModal').find('#modal-body').html(data.Data);
                //$('#dynModal').modal();
            }
        },
        error: function(data) {
            $('#dynModal').find('#modal-body').html("No fue posible cargar la informacion");
            //$('#dynModal').modal();
        }
    });
}

function RandomId() {
    var randLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));
    var uniqid = randLetter + Date.now();

    return uniqid;
}

function NewPopUp(options) {
 

    // clonar el popup
    var div = $('#popupModal');
    var klon = $(div).clone().prop('id', RandomId());
    $(klon).appendTo('body');

    $(klon).find('#popupModal-modal-content').html('<div style="padding: 30px;">' + $('#content-wait-modal').html() + '</div>');
    

    // Tamaño del Popup, por defecto
    var modalSize = "modal-lg";

    // Ver si se especificó un tamaño diferente
    if (options && options.modalSize) {
        switch(options.modalSize) {
            case 4:
            case 'xl':
                modalSize = 'modal-xl';
                break;
            case 3:
            case 'lg':
                modalSize = 'modal-lg';
                break;
            case 2:
            case 'md':
                modalSize = 'modal-md';
                break;
            case 1:
            case 'sm':
                modalSize = 'modal-sm'
                break;
        }
    }

    // Ajustar tamaño del popup
    $(klon).find('#modal-size').addClass(modalSize);

    if (options && options.dismissOnOutsideClick && options.dismissOnOutsideClick == false) {
        $(klon).modal({ backdrop : 'static'});
    }
    else {
        $(klon).modal();
    }    

    $(klon).on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    });

    $(klon).on('hidden.bs.modal', function (e) {
        $(klon).ClosePopUp();
    })

    $(klon).modal('show')

    return klon;
}

jQuery.fn.RefreshPopUp = function(content) {
    $(this).find('#popupModal-modal-content').html(content);
}

jQuery.fn.DismissPopUp = function() {
    $(this).modal('hide');
}

jQuery.fn.ClosePopUp = function() {    
    $(this).remove();
}

jQuery.fn.ErrorClosePopUp = function(error) {

    $(this).modal('hide');
    $(this).remove();

    Toast.fire({
        icon: 'error',
        title: error
    });
}

function openAjaxPostModal(title, description, ajaxurl, ajaxdata, method, onSuccessCallback = null, acceptButton = "", acceptValidateCallback = null, acceptCallback = null) {

    var modalBody = $('#dynModal').find('#modal-body');

    $(modalBody).html('Por favor espere...');
    $('#dynModal').find('#modal-title').html(title);
    $('#dynModal').find('#modal-description').html(description);

    $(modalBody).html($('#content-wait-modal').html());

    $('#dynModal-btn-aceptar').remove();
    $('#dynModal-btn-cerrar').remove(); 

    if (acceptButton!= "") {
        $('#dynModal-btn-aceptar-container').append('<button type="button" class="btn btn-success" id="dynModal-btn-aceptar"></button>&nbsp;&nbsp;');  
        $('#dynModal-btn-aceptar').text(acceptButton);
        $('#dynModal-btn-aceptar-container').removeClass('hide');

        $('#dynModal-btn-aceptar').click(function () {
            if (acceptValidateCallback != null) {
                if (acceptValidateCallback()) {
                    $('#dynModal').modal('hide');
                    $('#dynModal-btn-aceptar').remove();
                    $('#dynModal-btn-cerrar').remove();

                    if (acceptCallback != null) {
                        acceptCallback();
                    }
                }
            }
            else {
                $('#dynModal').modal('hide');
                $('#dynModal-btn-aceptar').remove();
                $('#dynModal-btn-cerrar').remove();
            }
        });
    }
    else {
        $('#dynModal-btn-aceptar').remove();
        $('#dynModal-btn-aceptar-container').html('');
        $('#dynModal-btn-aceptar-container').addClass('hide');  
    }

    $('#dynModal-btn-cerrar-container').append('<button type="button" class="btn btn-default" id="dynModal-btn-cerrar"></button>');
    $('#dynModal-btn-cerrar').text(Localize('BtnCerrar'));

    $('#dynModal-btn-cerrar').click(function () {
        $('#dynModal').modal('hide');
        $('#dynModal-btn-aceptar').remove();
        $('#dynModal-btn-cerrar').remove();        
    });

    $('#dynModal').modal();

    $.ajax({
        type     : method,
        contentType: "application/json; charset=UTF-8",
        dataType : 'json',
        url      : ajaxurl,
        data     : ajaxdata,
        success: function(data) {
            if (data.ErrorCode > 0) {
                $('#dynModal').find('#modal-body').html(data.ErrorMessage);
                //$('#dynModal').modal();
            }
            else {
                $('#dynModal').find('#modal-body').html(data.Result);

                if (onSuccessCallback) {
                    onSuccessCallback(ajaxdata);
                }
                //$('#dynModal').modal();
            }
        },
        error: function(data) {
            $('#dynModal').find('#modal-body').html("No fue posible cargar la informacion");
            //$('#dynModal').modal();
        }
    });
}

function openContentModal(title, description, content, color, nopre = false) {	
    $('#dynModal').find('#modal-title').html(title);
    $('#dynModal').find('#modal-description').html(description);

    if (nopre == true) {
        $('#modal-body_').find('#modal-body').html(content);
    }
    else {
        $('#modal-body_').find('#modal-body').html('<pre style="white-space: pre-line; word-break: normal; word-wrap: normal;">' + content + '</pre>');
    }

    $('#dynModal-btn-cerrar-container').append('<button type="button" class="btn btn-default" id="dynModal-btn-cerrar"></button>');
    $('#dynModal-btn-cerrar').text(Localize('BtnCerrar'));

    $('#dynModal-btn-cerrar').click(function () {
        $('#dynModal').modal('hide');
        $('#dynModal-btn-aceptar').remove();
        $('#dynModal-btn-cerrar').remove();        
    });
    
    $('#dynModal').modal();

    $('#modal-body_').scrollTop(0);
}

jQuery.fn.animateWidth = function(action, width, fnBefore, fnComplete) {

    if (action=="show" || action=="hide") {

        collapsed = $(this).attr('data-collapsed');
        element = $(this);

        if (action=="show" && (!(collapsed) || collapsed==1)) {
            
            if (fnBefore) {
                fnBefore();
            }

            // SHOW
            // Resetear primero
            $(this).addClass('hide');
            $(this).css('width', '0px');
            $(this).removeClass('hide');
            $(this).attr('data-collapsed', 0);

            $(this).animate({width: width}, function() {
                if (fnComplete) {
                    fnComplete();
                }
            });

        } 
        else if (action == "hide" && collapsed==0) {
            // HIDE
            // Resetear primero
            $(this).removeClass('hide');
            $(this).css('width', width);
            $(this).attr('data-collapsed', 1);

            $(this).animate(
                {width: '0px'}, 
                400,
                function() {
                    $(element).addClass('hide');

                    if (fnComplete) {
                        fnComplete();
                    }
                }
            );
        }
    }
}

jQuery.fn.animateRotate = function(startAngle, endAngle, duration, easing, complete){
    return this.each(function(){
        var elem = $(this);

        $({deg: startAngle}).animate({deg: endAngle}, {
            duration: duration,
            easing: easing,
            step: function(now){
                elem.css({
                  '-moz-transform':'rotate('+now+'deg)',
                  '-webkit-transform':'rotate('+now+'deg)',
                  '-o-transform':'rotate('+now+'deg)',
                  '-ms-transform':'rotate('+now+'deg)',
                  'transform':'rotate('+now+'deg)'
                });
            },
            complete: complete || $.noop
        });
    });
};

function GetErrorDescriptionsHtml(errors) {

    html = "";

    $(errors).each(function(index, item) {
        html = html + "<div>" + item + "</div>";
    });

    return html;
}

function DisplayErrors(errors, type = 'warning', title = 'Atención!', confirmButtonText = 'OK', confirmButtonColor = '#DD6B55', mensajePermanente = false) {

    var errores = GetErrorDescriptionsHtml(errors);
    
    if (mensajePermanente) {
        Swal.fire({
            icon:   type,
            html:   errores
        });
    }
    else {
        Toast.fire({
            icon: 'error',
            html: errores
        });
    }

}

function ShowLoader(params) { 

    var subtitle = "Comunicándose con el servidor";
    var title = "Por favor espere...";
    var fnComplete = null;

    if (params && params != 'undefined') {
        if (params.SubTitle != 'undefined') subtitle = params.SubTitle;
        if (params.Title != 'undefined') title = params.Title;
        if (params.fnComplete != 'undefined') fnComplete = params.fnComplete;
    }

    $('#pre-loader').animateWidth('show', 320,
        function() {
            $('#pre-loader-text').addClass('hide');
            $('#pre-loader-icon').addClass('hide');
            $('#pre-loader-title').html(title);
            $('#pre-loader-subtitle').html(subtitle);
        },
        function() {
            $('#pre-loader-text').fadeIn(300);
            $('#pre-loader-icon').fadeIn(350);
            $('#pre-loader-text').removeClass('hide');
            $('#pre-loader-icon').removeClass('hide');

            if (fnComplete != null) {
                fnComplete();
            }
        }
    );
}

function HideLoader(params) {

    var fnComplete = null;

    if (params && params != 'undefined') {
        if (params.fnComplete != 'undefined') fnComplete = params.fnComplete;
    }

    $('#pre-loader-icon').fadeOut(350);
    $('#pre-loader-text').fadeOut(200, function() {
        $('#pre-loader-text').addClass('hide');
        $('#pre-loader-icon').addClass('hide');
        $('#pre-loader').animateWidth('hide', 320, null, fnComplete);
    });

}

function DropTableRow(formId, rowPk) {

    var row = $('#' + formId + ' tr[data-pk="' + rowPk + '"]').remove();
    
}

function EvalJSFromHtml(html) {
    var newElement = document.createElement('div');
    newElement.innerHTML = html;
  
    var scripts = newElement.getElementsByTagName("script");
    for (var i = 0; i < scripts.length; ++i) {
      var script = scripts[i];
      eval(script);

      if (script.innerHTML) {
        eval(script.innerHTML);
      }
    }
  }

// function LoadContent(loadInfo, silence = false, postData = null) {
//     var funcError   = loadInfo.OnErrorCallback;
//     var funcSuccess = loadInfo.OnSuccessCallback;

//     if (!silence) {
//         $(loadInfo.ContentElement).html($('#content-wait-modal').html());
//     }

//     var method = 'post';
//     if (postData == null) {
//         method = 'get';
//     }

//     var entity = null;
//     if (postData != null) {
//         entity = postData;
//     }

//         $.ajax({
//             type: method,
//             data: entity,
//             url: loadInfo.ContentSourceUri,
//             success: function(data) {
//                 if (data.ErrorCode > 0) {
//                     if (funcError != null && typeof func === 'function') {
//                         funcError(data.ErrorCode, data.ErrorMessage);
//                     }
//                 }
//                 else {
//                     if (funcSuccess && typeof funcSuccess === 'function') {
//                         if (data.ResultType == 'ActionView') {
//                             $(loadInfo.ContentElement).html(data.ViewContent);

//                             // Ejecutar scripts que pudieran venir contenidos
//                             // if ( typeof load === 'function') {
//                             //     load();
//                             // }
//                             EvalJSFromHtml(data.ViewContent);                        

//                             // if ( typeof scriptContent === 'function') {
//                             //     scriptContent();
//                             // }

//                             funcSuccess();
//                         }
//                         else {
//                             $(loadInfo.ContentElement).html('');
//                             funcSuccess();
//                         }                    
//                     }
//                 }
//             },
//             error: function(data) {
//                 if (funcError != null && typeof funcError === 'function') {
//                     funcError(1, 'Ha ocurrido un error inesperado');
//                 }
//             }
//         });
// }

// Funcion para cargar contenido dinamico

function RefreshContent(loadInfo, silence = false, postData = null) {

    var funcError   = loadInfo.OnErrorCallback;
    var funcSuccess = loadInfo.OnSuccessCallback;

    if (!silence) {
        $(loadInfo.ContentElement).html($('#content-wait-modal').html());
    }

    var method = 'post';
    if (postData == null) {
        $(loadInfo.ContentElement).load(loadInfo.ContentSourceUri);
        return;
    }

    if (postData != null) {
        $(loadInfo.ContentElement).load(loadInfo.ContentSourceUri, postData);
        return;
    }

    return;
    
}

// Funcion para obtener todos los inputs de un formularo
function ReadFormElements(formId) {

    var result = {};

    $('.form-input[data-form-id="' + formId + '"]').each(function() {

        var object = $(this);
        var id = $(this)[0].id;        
        var objectName = id.replace(formId + '-', '');

        result[objectName] = object;
    });

    return result;
}

// Funcion para obtener los campos 'data' de elemento
function ReadData(element) {
    return $(element).data();
}

// Funcion para obtener el contenido completo de un formulario
function ReadForm (formId) {

    var result = {};

    $('.form-input[data-form-id="' + formId + '"]').each(function() {
        
        // Obtener el id del campo
        var id = $(this)[0].id;
        // Remover el nombre del formulario
        var objectName = id.replace(formId + '-', '');

        // Obtener el tipo de input
        var tipo = $(this)[0].type;

        var value = null;
        if (tipo == 'checkbox') {
            value = ($(this).is(':checked')) ? 1 : 0;
        }
        else {
            // Obtener el valor
            var value = $(this).val();
        }

        // Si es Select, obtener el OPTION seleccionado y sus campos DATA
        if (tipo != null) {
            switch(tipo) {
                case 'select-one':
                    option = $(this).find(":selected");
                    optionData = $(option).data();

                    // Agregar el option al objeto
                    if (optionData)
                        if (Object.entries(optionData).length > 0)
                            result[objectName + '_OptionData'] = optionData;
            }
        }

        // Agregar el valor al objeto
        result[objectName] = value;
    });

    return result;
}

// Funcion para actualizar una lista dependiente, cuando cambia la seleccion en la lista principal
function ActualizarListaDependiente(definition) {

    var dependantList       = definition.DependantList;
    var refreshService      = definition.RefreshService;
    var refreshAction       = definition.RefreshAction;
    var identifier          = definition.Identifier;
    var keyField            = definition.KeyField;
    var descriptionField    = definition.DescriptionField;
    var descriptionFunction = definition.DescriptionFunction;

    // Borrar la lista dependiente
    $(dependantList).empty();

    // Refrescar la lista con el servicio
    if (refreshService && refreshService != '') {
        var myServiceClass = window[refreshService + '_invoke'];
        var service = new myServiceClass();

        service.GetBy({ Id: JSON.stringify(identifier) },
            refreshAction,
            function(result) {
                $(result).each(function(indice, elemento) {

                    var newOption = $('<option>');
                    
                    // Obtener una versión del objeto transformada en arreglo
                    var obj = Object.entries(elemento);

                    var id = '';
                    var desc = '';

                    // Recorrer el objeto en forma de arreglo y obtener los campos buscados
                    $(obj).each(function(indice, item) {

                        if (item[0] == keyField && id == '') {
                            id = item[1];
                            $(newOption).val(id);
                        }
                        else if (item[0] == descriptionField && desc == '') {
                            desc = item[1];                            
                            $(newOption).text(desc);
                        }

                        //else {

                            $(newOption).attr('data-' + item[0], item[1]);

                        //}

                    });

                    if (typeof window[descriptionFunction] === 'function') {
                        $(newOption).text( window[descriptionFunction](elemento) );
                    }

                    // Agregar los campos encontrados como un nuevo OPTION al SELECT
                    $(newOption).appendTo(dependantList);

                    //var newOption = new Option(desc, id, false, false);
                    //$(dependantList).append(newOption);
                });

                // actualizar la lista SELECT2
                $(dependantList).trigger('change');
            },
            function(errorCode, errorMessage) {
                if (errorMessage && errorMessage != '') {
                    Toast.fire({
                        icon: 'warning',
                        title: errorMessage
                    });
                }
                else {
                    Toast.fire({
                        icon: 'error',
                        title: Localize("ErrorEstandar")
                    });
                }
            });

    }

}

Number.prototype.padLeft = function (n,str) {
    return (this < 0 ? '-' : '') + 
            Array(n-String(Math.abs(this)).length+1)
             .join(str||'0') + 
           (Math.abs(this));
}

jQuery.fn.TablaEstandar = function(CustomSettings,custombtn = false,customExcel = null, records = 10) {

    if(CustomSettings != null){
        
        custombtn = ( CustomSettings.CustomPdf == true && CustomSettings.HideAllButtons == false) ? true : false;

    }

    //------------------------------------------------
    // definición de si se muestra el btn custom o no
    //------------------------------------------------
    
    var btn = []; 
    if(CustomSettings != null && CustomSettings?.HideAllButtons == true ){

    }else{        
        if(customExcel != null && customExcel.ShowButton == false){
            
          
        }else if(customExcel != null && customExcel.ShowButton == true){
            btn.push(
                {
                    text: 'Excel',
                    className: 'btn-success',
                    action: function ( e, dt, node, config ) {
        
                        __customtableExcel(customExcel); 
                    }
                }
            ) ;
        }else{
            btn.push(
                {  
                    extend: 'excelHtml5',
                    title : 'PULSE - Reporte',
                    filename:'PULSE - Reporte',
                    exportOptions: {
                    columns: "thead th:not(.btnAccion)"
                }
            }
            ) ;
        }

        if(custombtn == true && CustomSettings != null){
            btn.push(
                {
                    text: 'PDF',
                    className: 'btn-danger',
                    action: function ( e, dt, node, config ) {
        
                        __CustomPdfMake(dt,CustomSettings); 
                    }
                }
            ) ;
        }else{
            btn.push(
                {  
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    title : 'PULSE - Reporte',
                    filename:'PULSE - Reporte',
                    exportOptions: {
                        columns: "thead th:not(.btnAccion)"
                    },
                    customize: function(doc) {    
                        var objLayout = {};
                        objLayout['hLineWidth']     = function(i) { return .8; };
                        objLayout['vLineWidth']     = function(i) { return .5; };
                        objLayout['hLineColor']     = function(i) { return '#eaeded'; };
                        objLayout['vLineColor']     = function(i) { return '#eaeded'; };
                        objLayout['paddingLeft']    = function(i) { return 8; };
                        objLayout['paddingRight']   = function(i) { return 8; };    
                        doc.content[1].layout = objLayout;    
                        doc.styles = {
                            tableHeader: {
                                bold:!0,
                                fontSize:11,
                                color:'black',
                                fillColor:'#E1E1E1',
                                alignment:'center'
                            },
                            lastLine: {
                                bold: true,
                                fontSize: 11,
                                color: 'blue'
                            },
                            defaultStyle: {
                                fontSize: 10,
                                color: 'black'
                            }
                        }   
                    }  
                }
            ) ;
        }
    }

    //------------------------------------------------
    var table = $(this).DataTable({
		"language"  : {
			"sProcessing":     "Procesando...",
			"sLengthMenu":	   "Mostrar _MENU_ registros",
		    "sZeroRecords":    "No se encontraron resultados",
		    "sEmptyTable":     "Ningún dato disponible en esta tabla",
		    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
		    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
		    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		    "sInfoPostFix":    "",
		    "sSearch":         "&nbsp;&nbsp;Buscar:",
		    "sUrl":            "",
		    "sInfoThousands":  ",",
		    "sLoadingRecords": "Cargando...",
		    "oPaginate": {
		        "sFirst":    "Primero",
		        "sLast":     "Último",
		        "sNext":     "Siguiente",
		        "sPrevious": "Anterior"
		    },
		    "oAria": {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
		},
        "pageLength" : records,
        "order": [] ,
        "aaSorting": [],
        'buttons':   btn
    }); 

    //Muestra los botones de exportacion de la tabla

    table.buttons().container()
    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );

    return table;

}

function __customtableExcel(settings = {}){
    console.log("🚀 ~ file: app.js ~ line 1203 ~ __customtableExcel ~ settings", settings)

    if(settings?.Controller != null && settings?.FileName != null){
        window.open(`/api/table/${settings.Controller}?filename=${settings.FileName}&estado=${settings.Estado}`, '_blank');
    }else{
        console.error('error')
    }
    
} 

function __CustomPdfMake(datatable,__CustomPdfSettings){
   
    // ------------------------------------------------------------------------------------------------
    // COMIENZO DE LA GENERACIÓN DEL PDF - AVISO DE GENERACIÓN 
    // ------------------------------------------------------------------------------------------------
    Swal.fire({
        icon: 'info',
        title: 'Generando PDF',
        text: 'Esto puede tardar unos segundos',   
        showCancelButton: false,  
        showConfirmButton: false 
    })

    // ------------------------------------------------------------------------------------------------
    // FORMATEO DE LA INFORMACIÓN EXISTENTE EN LA TABLA
    // ------------------------------------------------------------------------------------------------

    var headers = [];
    var data    = [];
    var table_columns = datatable.columns().header();
    var table_data = datatable.rows().data();
    var username  = $('#UsernameContainer_').text() ? $('#UsernameContainer_').text() : "";

    for(var i = 0; i < table_data.length; i++){    
        
      
        var longitud    = table_data[i].length;
        var btn_cell    = (__CustomPdfSettings.TableHasButtons == true ) ?  longitud - 1 : longitud + 1;

        var microarray = [];
        
        // regex que busca todo los divs con clase ROW
        const   RegexByClass    = /<.*class\s*=\s*.*row.>/g;

        // regex que busca todos los divs con clase col-md- (del 1 al 11)
        const   RegexByCol      = /<.*class\s*=\s*.*col-md-\b([1-9]|[10][0-1])\b.>/g;

        // regex que busca todos los divs con clase col-md-12
        const   RegexCol12      = /<.*class\s*=\s*.*col-md-12.>/g;
        
        $.each(table_data[i], function( index, value ) {

                var txt = " ";
                
                if(value instanceof String){
                    txt = value.replaceAll(RegexByClass,'----!!!----!!!----!!!----!!!---');
                    txt = txt.replaceAll(RegexByCol,'----!!!-123123---!!!---123123-!!!--123123--!!!-123123--')
                    txt = txt.replaceAll(RegexCol12,'----!!!-qqqq---!!!---qqqq-!!!--qqq--!!!-qqq--')
                    txt = $.htmlToText(`<div> ${txt} </div>`);                   
                }else{    
                    if(value.display){
                        
                        txt = value.display.replaceAll(RegexByClass,'----!!!----!!!----!!!----!!!---');
                        txt = txt.replaceAll(RegexByCol,'----!!!-123123---!!!---123123-!!!--123123--!!!-123123--')
                        txt = txt.replaceAll(RegexCol12,'----!!!-qqqq---!!!---qqqq-!!!--qqq--!!!-qqq--')
                        txt     = $.htmlToText(`<div> ${txt} </div>`);

                    }else{                        
                        txt = value.replaceAll(RegexByClass,'----!!!----!!!----!!!----!!!---');
                        txt = txt.replaceAll(RegexByCol,'----!!!-123123---!!!---123123-!!!--123123--!!!-123123--')
                        txt = txt.replaceAll(RegexCol12,'----!!!-qqqq---!!!---qqqq-!!!--qqq--!!!-qqq--')
                        txt =   $.htmlToText(`<div> ${txt} </div>`);
                    }                        
                }
                // Se eliminan los espacios en blanco con mas de una repetición
                txt = txt.replace(/\s+/g, ' ').trim();

                // se remplazan las etiquetas limpiadas por saltos de linea
                txt = txt.replaceAll('----!!!----!!!----!!!----!!!---',"\n");
                txt = txt.replaceAll('----!!!-qqqq---!!!---qqqq-!!!--qqq--!!!-qqq--',"\n");
                txt = txt.replaceAll('----!!!-123123---!!!---123123-!!!--123123--!!!-123123--',"\n");

                txt = txt.trim();              

                if(index != btn_cell){
                    microarray.push(txt);
                }
        }); 

        data.push(microarray);
    }

    var headerlimit  = (__CustomPdfSettings.TableHasButtons == true ) ? table_columns.length - 1 : table_columns.length + 1;
    for(var i = 0; i < table_columns.length; i++){  
        
        if(i != headerlimit ){
            headers.push(table_columns[i].innerText);
        }
      
    }
    // ---------------------------------------------------------------------------------------------------------------
    // funcion que permite agregar el footer y el header al pdf (de manera forzada)
    // ---------------------------------------------------------------------------------------------------------------
    const addFooters = (doc,LogoEmpresa,LogoShutdown,username) => {
        const pageCount = doc.internal.getNumberOfPages()
      
        doc.setFont('helvetica', 'italic')
        doc.setFontSize(8)

        doc.setLineDash = function (dashArray, dashPhase) {
            if (dashArray == undefined) {
                this.internal.write('[] 0 d')
            } else {
                this.internal.write('[' + dashArray + '] ' + dashPhase + ' d')
            }
        }

    

        
        var _pageHeight = 216 - 5; 
        var _pageWidth = 356   - 5;
    
        //SE GENERA UNA GRILLA PARA INSERTAR COMPONENTES
        var _HGrid = _pageHeight / 30;   
        var _WGrid = _pageWidth  / 10;   


        for (var i = 2; i <= pageCount; i++) {
          doc.setPage(i)
           

          doc.setFontSize(9); 
          doc.text(username, _WGrid * 0.5, _HGrid * 28);
          //TEXTO DEBAJO DEL USERNAME
          doc.setFontSize(8); 
          doc.text('Generado por', _WGrid * 0.5, _HGrid * 28.4);
          
          //SE MUESTRA LA FECHA EN LA QUE SE GENERO
          doc.setFontSize(9); 
          doc.text(FechaOnlyDate, _WGrid * 9.5, _HGrid * 28, 'right');
          //DEBAJO SE MUESTRA LA HORA EN LA QUE SE GENERO
          doc.setFontSize(8); 
          doc.text(FechaOnlyHour, _WGrid * 9.5, _HGrid * 28.4 , 'right');
      
          doc.setDrawColor(  191, 191, 191  );
          doc.setTextColor(   191, 191, 191  ); 
          doc.setLineDash(1,6);
          doc.setLineWidth(0.4);  
      
          doc.line(_WGrid * 0.5, _HGrid * 28.7, _WGrid * 9.5, _HGrid * 28.7, 10);             
          doc.addImage(LogoEmpresa, 'JPEG', _WGrid * 0.5,  _HGrid * 29, 35, 9);
      
          
          doc.addImage(LogoShutdown, 'JPEG', _WGrid * 9,  _HGrid * 29, 9, 9);

            
        }
    }

    const addHeader = (doc) => {
        const pageCount = doc.internal.getNumberOfPages()
      
        doc.setFont('helvetica', 'italic')
        
        doc.setFontSize(8);

        var _pageHeight = 216 - 5; 
        var _pageWidth = 356   - 5;
    
        //SE GENERA UNA GRILLA PARA INSERTAR COMPONENTES
        var _HGrid = _pageHeight / 30;   
        var _WGrid = _pageWidth  / 10;   

        doc.setLineDash = function (dashArray, dashPhase) {
            if (dashArray == undefined) {
                this.internal.write('[] 0 d')
            } else {
                this.internal.write('[' + dashArray + '] ' + dashPhase + ' d')
            }
        }
        
        var _FechaGen = new Date();
        var _txt_Date = _FechaGen.toLocaleDateString('es-ES', options).toUpperCase();

        
        for (var i = 2; i <= pageCount; i++) {
            doc.setPage(i)

            
            
            doc.setFontSize(9); 
            doc.setTextColor(  117, 119, 119  );  
            doc.setFontStyle('normal');
            doc.text('CASERONES', _WGrid * 0.5, _HGrid * 1); 
            doc.text(_txt_Date, _WGrid * 9.5, _HGrid * 1, 'right');
            doc.setFontSize(8); 
            doc.text('Lumina Copper Chile', _WGrid * 0.5, _HGrid * 1.5);  
            doc.text('Fecha', _WGrid * 9.5, _HGrid * 1.5, 'right');

            doc.setDrawColor(  191, 191, 191  );
            doc.setTextColor(   191, 191, 191  ); 
            doc.setLineDash(1,6);
            doc.line(_WGrid * 0.5, _HGrid * 1.7, _WGrid * 9.5, _HGrid * 1.7, 10);
        }
    }

    
    // ------------------------------------------------------------------------------------------------
    // SE INSTANCIA EL JSPDF PARA COMENZAR A ESTRUCTURAR EL PDF
    // ------------------------------------------------------------------------------------------------
    var doc         = new jsPDF('l', "mm", 'legal',true);
    // ------------------------------------------------------------------------------------------------
    // SE BUSCA Y SE DESCARGA LA IMAGEN DEL LOGO - Y SE GENERA LA CONFIGURACIÓN DE LA PORTADA
    // ------------------------------------------------------------------------------------------------
    
    const UrlLogoEmpresa  = $('.logo-empresa-imagen').attr('src');
    const UrlLogoShutDown = '/images/LogoShutdownNewFondoRojo-180x180.png';
  
    //GENERA UNA NUEVA FECHA
    const FechaGen = new Date();

    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

    //SE ASIGA EL NOMBRE DE LA FECHA (VIERNES, DIA TANTO MES TANTO AÑO TANTO)
    const txt_Date = FechaGen.toLocaleDateString('es-ES', options).toUpperCase();
    
    //CONSTANTE CON LA FECHA DE GENERACIÓN
    const FechaOnlyDate = FechaGen.getFullYear()+'/'+(FechaGen.getMonth()+1)+'/'+FechaGen.getDate();
    
    // CONSTANTE CON LA HORA DE GENERACIÓN
    const FechaOnlyHour = FechaGen.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute:'2-digit'
    });

    // DEFINICION DEL TAMAÑO DEL PDF (LETTER)
    const pageHeight = 216 - 10; 
    const pageWidth = 356   - 10;

    //SE GENERA UNA GRILLA PARA INSERTAR COMPONENTES
    const HGrid = pageHeight / 30;   
    const WGrid = pageWidth  / 10;   

    //configuracione del titulo
    const Settings = {
        FontSize: 13,
        TextColor: 40,
        FontStyle : 'normal'
    }

    // ------------------------------------------------------------------------------------------------
    // SE AGREGA LA PORTADA
    // ------------------------------------------------------------------------------------------------
    
    getBase64FromUrl(UrlLogoEmpresa).then(
        function(LogoEmpresa){
        // UNA VEZ OBTENIDO EL RESULTADO SE LLAMA LA IMAGEN SIGUIENTE
            getBase64FromUrl(UrlLogoShutDown).then(

                function(LogoShutdown) {

                    /**
                    * 
                    * GENERACION DEL HEADER DEL PDF
                    * 
                    */

                    doc.setFontSize(Settings.FontSize); 

                    // SE AGREGA EL LOGO DEL CLIENTE EN LA PARTE SUPERIOR
                    doc.addImage(LogoEmpresa, 'JPEG',(pageWidth / 2 ) - 36, HGrid * 3, 68, 24);

                    //SE AGREGA UN SUBTITULO AL LOGO CON LA PLATABRA REPORTE
                    txt      = "REPORTE"; // String que se imprime
                    txtWidth = doc.getStringUnitWidth(txt)*Settings.FontSize/doc.internal.scaleFactor; //Se calcula el with del texto
                    x        = ( pageWidth - txtWidth ) / 2; // Se calcula el padding para centrar el texto       
                    doc.text(txt, x, HGrid * 10); // se imprime el texto


                    // Se Muestra la fecha de generación         
                    txt      =  txt_Date;
                    txtWidth = doc.getStringUnitWidth(txt)*Settings.FontSize/doc.internal.scaleFactor;
                    x        = ( pageWidth - txtWidth ) / 2;        
                    doc.text(txt, x, HGrid * 14);

                    //Se asigna el nombre del pdf como titulo
                    txt      = __CustomPdfSettings.TituloPdf.toUpperCase();
                    txtWidth = doc.getStringUnitWidth(txt)*Settings.FontSize/doc.internal.scaleFactor;
                    x        = ( pageWidth - txtWidth ) / 2;        
                    doc.text(txt, x, HGrid * 12); 

                    //SE GENERA UNA CAJA DONDE SE ENCUENTRAN LOS DATOS DEL TIPO DE RESUMEN Y QUIEN LO GENERO
                    doc.setDrawColor( 197, 197, 197 );
                    let XRECTANGLE = (pageWidth -5);
                    doc.roundedRect(
                        (( pageWidth - XRECTANGLE ) / 2)+ 2,  //POSICIÓN X
                        HGrid * 18, //POSICION Y
                        XRECTANGLE, // ANCHO
                        30, // ALTO,
                        3,
                        3,
                        'S' //TIPO RELLENO (F => FILL | S => BORDER  | DF => FILL AND BORDER)
                    );

                    //ESTILOS DEL TEXTO
                    doc.setFontSize(11); 
                    doc.setTextColor(  64, 66, 6 );
                    doc.setFontStyle('bold');

                    //SE DEFINE QUE TIPO DE DOCUMENTO ES
                    txt      = __CustomPdfSettings.Modulo;
                    txt      = txt.toUpperCase();       
                    doc.text(txt, WGrid * 1, HGrid * 19);

                    //SE DEFINE EL NOMBRE DE USUARIO QUE GENERO EL DOCUMENTO
                    txt      = username.toUpperCase();      
                    doc.text(txt, WGrid * 9, HGrid * 19, 'right'); 

                    // SE PROCEDE A GENERAR UN SUBTITULO AL TIPO DE DOCUMENTO Y AL USUARIO
                    doc.setFontSize(10); 
                    doc.setTextColor(  117, 119, 119  );
                    doc.setFontStyle(Settings.FontStyle);

                    //SUBTITULO DEL TIPO DE DOCUMENT
                    txt      =  ( __CustomPdfSettings.OcultarSubtituloModulo == true ) ? "" : 'MÓDULO';      
                    doc.text(txt, WGrid * 1, HGrid * 19.8);

                    //SUBTITULO DEL USUARIO EN SI
                    txt      = 'GENERADO POR';   
                    doc.text(txt, WGrid * 9, HGrid * 19.8, 'right');

                    //UNA VEZ COMPLETADO TODO LO ANTERIOR SE AGREGA UNA NUEVA PAGINA EN BLANCO
                    doc.addPage();

                    // FIN DE LA GENERACION DE LA PRIMERA PAGINA DEL PDF  
                    
                    
                    // ------------------------------------------------------------------------------------------------
                    // SE AGREGA LA TABLA AL PDF
                    // ------------------------------------------------------------------------------------------------
                    
                    doc.autoTable(headers, data,
                        {
                            margin: {top: 20, bottom: 30}, 
                            theme: 'grid',    
                            pageBreak: 'auto',
                            rowPageBreak: 'avoid',  
                            headStyles: {
                                fillColor: [248, 248, 248], 
                                halign : 'center',
                                valign : 'middle',
                                fillType: "FD",
                                fontSize: 8,
                                textColor: [0, 0, 0],
                                fontStyle: 'normal', 
                                cellWidth : "wrap",
                            },
                            bodyStyles: {
                                
                            }
                            
                            

                        }
                        
                    );
                        
                    addFooters(doc,LogoEmpresa,LogoShutdown,username);

                    addHeader(doc);
                    // ------------------------------------------------------------------------------------------------
                    // SE GENERA Y SE EXPORTA EL PDF
                    // ------------------------------------------------------------------------------------------------
                    
                    doc.save('PULSE - Reporte.pdf');

                    Swal.fire({
                        icon:   'success',    
                        text:   'Archivo Generado'
                    });            

                }
            )
        }
    );    
  
}


var Toast;

$(function () {

    Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    $('.menu-detencion').click(function() {
        // Se selecciono un cambio de DETENCION en el menu del HEADER

        // Obtener el header
        var idDetencion = $(this).attr('data-id-detencion');
        var esArchivada = $(this).attr('data-es-archivada')
        var menu = $(this).parent().parent().parent();

        if (menu) {
            $(menu).removeClass('open');
        }

        var Detencion =  {
            IdDetencion: idDetencion,
            EsArchivada: esArchivada
        }

        // Modificar la detencion en el servidor
        $.ajax({
            contentType: "application/json; charset=UTF-8",
            dataType : 'json',
            type: 'POST',
            url: '/api/mantencion/cambiardetencion',
            data: JSON.stringify(Detencion),
            success: function (data) {

                if (data && data.ErrorCode == 0) {
                    location.reload(true);
                    //location.replace('/mantencion/activas');
                }
                else {                    
                    Toast.fire({
                        icon: 'error',
                        title: Localize("ErrorEstandar")
                    });
                    //toastr.error("Ha ocurrido un error"); 
                }
            },
            error: function () {
                Toast.fire({
                    icon: 'error',
                    title: Localize("ErrorEstandar")
                });
                //toastr.error("Ha ocurrido un error");
            }
        });

        return false;
    });


});

$.LoadingSpinner = function() {
    return '<i class="fa fa-spinner fa-pulse fa-fw"></i>';
}


// ----------------------------------------------------------
// Levanta el popup que permite ver las evidencias
// ----------------------------------------------------------
function BtnEvidenciasMontaje_OnClick(eventinfo){

    var idBoton = $(eventinfo.Element)[0].id;
    var rowKey  = eventinfo.RowData.pk;

    var entity = {

        Montaje : JSON.stringify(eventinfo.RowData.idmonmontaje),
        IdBoton : JSON.stringify(idBoton) ,
        RowKey  : rowKey,

    }

    console.log(entity);

    var popup = new NewPopUp();

    const service = new EvidenciaSvc();

    service.PopUpEvidencias(
        entity,
        function(result){
            popup.RefreshPopUp(result);
        },
        function(errorCode,errorMessage){
            popup.ErrorClosePopUp(errorMessage);
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )

}

// ----------------------------------------------------------
// 
// ----------------------------------------------------------

function BtnSubirEvidencia_OnClick(eventInfo) {    

    console.log(eventInfo);
  
    var entity = {
        IdMonMontaje : eventInfo.FormData.IdMonMontaje
    }

    const service = new EvidenciaSvc();

    var popupSubirEvidencia = new NewPopUp();

    service.PopUpSubirEvidencia(
        entity,
        function(result){
           popupSubirEvidencia.RefreshPopUp(result);
        },
        function(errorCode,errorMessage){
           popupSubirEvidencia.ErrorClosePopUp(errorMessage);
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
    )

}

function BtnSubirArchivos_OnClick(eventInfo){
 
    var boton = eventInfo.Element;

    $(boton).prop('disabled', true);

    $('#formSubirArchivos-fileInput').fileinput('upload').fileinput('disable');
}

jQuery.fn.RefreshBadge = function (badge) {

    $(this).each(function() {
        var badgeElement = $(this).find('.badge-element');
        var badgeTextElement = $(this).find('.badge-text-element');

        if (badgeElement && badgeTextElement) {
            // Ver si ocultamos o mostramos
            if (badge && badge != '') {
                $(badgeTextElement).html(badge);
                $(badgeTextElement).data('badge', badge);
                $(badgeElement).removeClass('hide');
            }
            else {
                $(badgeTextElement).html('');
                $(badgeTextElement).data('badge', '');
                $(badgeElement).addClass('hide');
            }
        }
    });
}


function JumpTo(DestinationUrl, PostData, ShowLoader = true, LoaderTitle = 'Por favor espere...', LoaderText = 'Actualizando')
{

    if (ShowLoader) {
        Swal.fire({
            title: LoaderTitle,
            text: LoaderText,
            showConfirmButton: false
        });
    }

    var Html='';
    Html += "<form id='js_navigate_with_post' method='post' action='" + DestinationUrl + "'>\n";
    Html +=  "<input type='hidden' name='js_navigate_with_post' value='true'>\n";

    for (var ParamIndex in PostData) {
        if (PostData.hasOwnProperty(ParamIndex))
        {
            Html +=  "<input type='hidden' name='" + ParamIndex + "' value='" + PostData[ParamIndex] + "'>\n";
        }
    }

    Html += "</form>\n";

    jQuery('body').append(Html);
    jQuery('#js_navigate_with_post').submit();
}
 



function reiniciarEstiloValidacionInput(idInput){
    $(`#${idInput}`).removeClass('valid');
    $(`#${idInput}`).removeClass('invalid');
}
function ValidarInput(idInput,CondicionValidante,SuccessMessage,ErrorMessage){

    /* capturamos el input */
    var element         =   $(`#${idInput}`)
    var element_span    =   $(`#${idInput}-span`)

    /* extraemos el valor del input */
    var value = element.value ? element.value : $(element).val(); ;
     
    /* ejecutamos la condicion solicitada */
    var condition       =   CondicionValidante(value);
    var classToAdd      =   '';
    var messageToShow   =   '';
    var returnstatus    =   true;

    if (condition == true){
        classToAdd      =   'valid'
        messageToShow   =   SuccessMessage;
        returnstatus    =   true;
    }else {
        classToAdd      =   'invalid'
        messageToShow   =   ErrorMessage;
        returnstatus    =   false;
    }
    /* modificamos los estilos del input */
    reiniciarEstiloValidacionInput(idInput)
    element.addClass(classToAdd);
    
    /* modificamos los estilos del span */
    reiniciarEstiloValidacionInput(`${idInput}-span`)
    element_span.addClass(classToAdd);
    element_span.text(messageToShow);
    return returnstatus;
}


function validarTextoInput(x){
    
    if (x == null)
        return null;

    x.replaceAll(">","")
    x.replaceAll("<","")
    x.replaceAll("/","")
    x.replaceAll("'","")
    x.replaceAll('"',"")
    x.replaceAll("`","") 
    x.replaceAll("-","")
    x.trim();
    return x;
}


function validarTextoInputNoSpecialChar(x){
    
    if (x == null)
        return null;

    var rgx = new RegExp()

    x.replaceAll(">","")
    x.replaceAll("<","")
    x.replaceAll("/","")
    x.replaceAll("'","")
    x.replaceAll('"',"")
    x.replaceAll("`","") 
    x.replaceAll("-","")
    x.replaceAll("@","")
    x.replaceAll("\\","")
    x.replaceAll(".","")
    x.replaceAll(",","")

    x.trim();
    return x;
}

const validateFormatoEmail = (email) => {
    email   =   validarTextoInput(email);
    return String(email)
      .toLowerCase()
      .match(
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      );
};

