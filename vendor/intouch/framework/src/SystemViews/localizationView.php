<?php

// Agregar los mensajes en el script de la pagina
define('IDIOMAS', true);

use Intouch\Framework\Configuration\MensajeConfig;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\View\Display;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Mensajes\Mensaje;

$mensajes = MensajeConfig::Instance(); // $GLOBALS['conn_mensajes'];
$html = "";

foreach($mensajes as $messageID => $entries) {

    if ($html != "") {
        $html .= ",\n";
    }

    $languages = "";

    foreach($entries as $language => $entry) {
        if ($languages != "") {
            $languages .= ",";
        }

        $languages .= "['$language', '$entry']";
    }

    $html .= "['$messageID', [$languages]]";
}
?>

<script>
<?php
$producto = (isset(Session::Instance()->producto)) ? Session::Instance()->producto : DEFAULT_PRODUCT;
$idioma   = (isset(Session::Instance()->idioma)) ? Session::Instance()->idioma : DEFAULT_LANGUAGE;

?>
    var Producto = "<?=$producto?>";
    var Locale = "<?=$producto . '_' . $idioma?>";
    var Mensajes = [
<?=$html?>
    ];

    function ObtenerMensaje(messageID) {

        return Mensajes.filter(function(element, index) {
            return (element[0] == messageID);            
        });
    }

    function ActualizarMensajes(Traduccion) {        

        // Agregar el mensaje
        entries = [];

        // Crear el arreglo con las traducciones
        console.log("Mensaje: " + Traduccion.MessageId);
        Traduccion.Entries.forEach(function(value, index) {
            entries.push([value[0], value[1]]);
            console.log(value[0] + ": " + value[1]);
        });

        var mensaje = EncontrarMensaje(Traduccion.MessageId);

        if (mensaje && mensaje != null) {
            // Sólo actualizamos los entries
            mensaje[1] = entries;
        }
        else {
            // Agregamos el mensaje nuevo a la lista de traduccion
            Mensajes.push([Traduccion.MessageId, entries]);
        }
    }

    function EncontrarMensaje(messageID) {
        var found = null;

        for(idxMessage=0; idxMessage < Mensajes.length; idxMessage++) {
            message = Mensajes[idxMessage];

            if (message[0] == messageID) {
                return message;
            }
        }

        return null;
    }

    function Localize(messageID, editable = false) {

        /*
        ['LblUsermenuMicuenta', [['es', 'Mi cuenta'],['en', 'My account']]],
        ['LblUsermenuCambiarclave', [['es', 'Cambiar mi clave'],['en', 'Change my password']]],
        ['LblUsermenuCerrarsesion', [['es', 'Cerrar sesión'],['en', 'Logout']]],
        */
        var found = null;
        var mensaje = EncontrarMensaje(messageID);

        if (mensaje && mensaje != null) {
            var Entries = message[1];

            // recorrer los entries
            for (idxEntry=0; idxEntry < Entries.length; idxEntry++) {
                entry = Entries[idxEntry];
                if (entry[0] == Locale) {
                    found = entry[1];
                    break;
                }
            }
        }

       if (!editable) {
           return found;
       }
       else {
           result = LocalizeEditable(messageID, found);
           return result;
       }
    }

    function LocalizeEditable(messageID, mensaje) {

        <?php
            // Obs: dado que desde la última utilización del renderer puede haber habido modificaciones
            //      en los parámetros de sesión, debemos forzar la creación de un renderer nuevo
            $parts = Display::GetRenderer('', true)->ObtenerPrefixSufix('[[[messageIdPlaceHolder]]]');        
        ?>
        var prefix = "<?=$parts[0]?>";
        var sufix = "<?=$parts[1]?>";

        var result = prefix.replace("[[[messageIdPlaceHolder]]]", messageID) + mensaje + sufix.replace("[[[messageIdPlaceHolder]]]", messageID);

        return result;
    }

    function EditarMensaje(elemento) {
        var messageId = $(elemento).data('messageid');
        var objMensaje = $('.message-content[data-messageid="' + messageId + '"]');
        var locale = $(objMensaje).data('locale');
        var alternativo = $(objMensaje).data('alternativo');

        var Mensaje = {
            MessageId: messageId,
            Alternativo: alternativo
        }

        // Obtener los mensajes en los diferentes idiomas
        $.ajax({
            dataType : 'json',
            type: 'POST',
            url: '/api/core/mensajes',
            data: { 
                Mensaje: JSON.stringify(Mensaje)
            }, //JSON.stringify(Mensaje),
            success: function (data) {                

                if (data.ResultType == 'ActionView') {   
                    var mensajes = data.ViewContent;

                    if (mensajes) {
                        $('#language-modal-body').empty();
                        $('#language-modal-body').append(mensajes);
                        $('#language-modal-title').html(messageId);

                        $('#hidLanguageMessageId').val(messageId);
                        $('#hidLanguageLocale').val(locale);

                        $('#languageModal').modal();
                    }
                }
                else if (data.ResultType == 'Error') {
                    Toast.fire({
                        icon: 'error',
                        title: data.ErrorMessage
                    });
                    //toastr.error(data.ErrorMessage);
                }
                else {
                    Toast.fire({
                        icon: 'error',
                        title: Localize("ErrorEstandar")
                    });
                   // toastr.error(Localize("ErrorEstandar"));
                }

            },
            error: function () {
                Toast.fire({
                    icon: 'error',
                    title: Localize("ErrorEstandar")
                });
                //toastr.error(Localize("ErrorEstandar"));
            }
        });
    }

    function CambiarIdioma(nuevoIdioma) {

        var Idioma = {
            CodigoIdioma: nuevoIdioma
        };

        $.ajax({
            dataType : 'json',
            type: 'POST',
            url: '/api/core/cambiaridioma',
            data: { 
                Idioma:  JSON.stringify(Idioma) 
            },
            success: function (data) {
                var data = JSON.parse(data.Result);
                // Variable global
                Locale = Producto + "_" + nuevoIdioma;
                $('#lblIdioma').html(nuevoIdioma);

                if (data && data.ErrorCode == 0) { 

                    // Recorrer todos los elementos y cambiar el idioma
                    $('.message-content').each(function() {
                        var messageID = $(this).data('messageid');
                        //AsignarIdioma(messageID, nuevoIdioma);
                        AsignarIdioma(messageID, Locale, 'content');

                        // Actualizar el Locale actual
                        $(this).data('locale', Locale);
                    });

                    // Recorrer los placeholders y cambiar idioma
                    $('.update-placeholder-onlanguagechange').each(function() {
                        var messageID = $(this).data('messageid');
                        //AsignarIdioma(messageID, nuevoIdioma);
                        AsignarIdioma(messageID, Locale, 'placeholder');
                    })

                    // Recorrer los options y cambiar idioma
                    $('.update-option-onlanguagechange').each(function() {
                        var messageID = $(this).data('messageid');
                        //AsignarIdioma(messageID, nuevoIdioma);
                        AsignarIdioma(messageID, Locale, 'option');
                    });

                    // Actualizar el idioma de TODOS los elementos de la pantalla actuales
                    //$('.message-content').data('locale', Locale);
                }
            }
        });
    }

    function AsignarIdioma(messageID, nuevoIdioma, tipo = 'content') {
        
        // Encontrar la nueva traduccion según el idioma seleccionado
        // [0]['LblUsermenuMicuenta', [['es', 'Mi cuenta'],['en', 'My account']]]
        // [1]['LblUsermenuMiPerfil', [['es', 'Mi perfil'],['en', 'My profile']]]
        //
        // La funcion debe filtrar y entregar un arreglo con máximo 1 elemento (elemento [0])
        var mensajesEncontrados = ObtenerMensaje(messageID);
        
        var newEntry = null;
        if (mensajesEncontrados && mensajesEncontrados[0] && mensajesEncontrados[0][0] && mensajesEncontrados[0][1]) {

            var mensajes = mensajesEncontrados[0];
            var etiqueta = mensajes[0];
            var entries  = mensajes[1];

            // entries
            // [[0]['es', 'Mi cuenta'],[1]['en', 'My account']]
            //
            // La funcion debe filtrar y entregar un elemento único o null            
            newEntries = entries.filter(function (value, index) {
                if (value[0] && value[1]) {
                    if (value[0] == nuevoIdioma) {
                        return true;
                    }                    
                }
                return false;
            });

            // Si se encontró la nueva traducción, entonces modificar todos los elementos que existan en pantalla con este MessageID
            if (newEntries && newEntries[0] && newEntries[0][0] && newEntries[0][1]) {
                var newEntry = newEntries[0];
                var idioma = newEntry[0];
                var entry = newEntry[1];

                switch(tipo) {
                    case 'content':
                        // Recorrer todos los elementos que utilicen este messageID
                        $('.message-content[data-messageid="' + messageID + '"]').html(entry);
                        break;
                    case 'placeholder':
                        $('.update-placeholder-onlanguagechange[data-messageid="' + messageID +'"]').each(function() {
                            $(this).attr('placeholder', entry);
                        });
                        break;
                    case 'option':
                        $('.update-option-onlanguagechange[data-messageid="' + messageID +'"]').each(function() {
                            $(this).text(entry);
                            $(this).parent().trigger('change');
                        });
                        break;
                }
            }
        }
    }

    function ActivaEventosTranslate() {

        $('.toggle-message-edit').click(function () {
            // Agregar el evento click as los elementos en pantalla (que no lo tengan ya)
            $('.message-edit.new').click(function() {
                event.preventDefault();
                event.stopPropagation();
                EditarMensaje($(this));
            });
            // Quitar la clase "new" para no agregarle los eventos en posteriores clics del boton Translate
            $('.message-edit.new').removeClass('new');

            $('.message-edit').toggle();
        });

    }

    $(function() {
        // Guardar una traduccion
        $('#btn-guardar-traduccion').click(function() {

            var messageId = $('#hidLanguageMessageId').val();
            var currentLocale = $('#hidLanguageLocale').val();
            var currentEntry = "";

            // Recorrer las traducciones
            var entries = [];
            var complete = true;

            $('.language-entry').each(function (item) {
                var entryLocale = $(this).data('locale');
                var entryInfo = $(this).val();

                if (entryInfo.trim() == "") {
                    complete = false;
                    return false;
                }

                if (entryLocale == currentLocale) {
                    currentEntry = entryInfo;
                }

                entries.push([entryLocale, entryInfo]);
            });

            if (!complete) {
                swal({
                    title: Localize("LblAtencion"),
                    text: "Se deben especificar todos idiomas a traducir",
                    showCancelButton: false,
                    type: "warning",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
            }

            var Traduccion = {
                MessageId: messageId,
                Entries: entries
            }

            $.ajax({
                dataType : 'json',
                type: 'POST',
                url: '/api/core/traducir',
                data: {
                    Mensaje: JSON.stringify(Traduccion)
                },
                success: function (data) {

                    if (data && data.ErrorCode == 0) {
                        
                        $('#language-modal-body').empty();                    

                        $('#hidLanguageMessageId').val('');
                        $('#hidLanguageLocale').val('');

                        // actualizar los textos dados por este MessageId
                        //$('.message-content[data-messageid="' + messageId + '"][data-locale="' + currentLocale + '"]').html(currentEntry);
                        $('.message-content[data-messageid="' + messageId + '"]').html(currentEntry);
                        // actualizar el color del boton
                        $('.message-edit[data-messageid="' + messageId + '"]').removeClass('btn-purple');
                        $('.message-edit[data-messageid="' + messageId + '"]').addClass('btn-success');

                        $('#languageModal').modal('hide');
                        
                        // Si el messageID es nuevo, no estará presente en el arreglo maestro
                        // Lo debemos agregar
                        ActualizarMensajes(Traduccion);
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
        });

        // Poner boton de traducción en los modales
        <?php
        if (SystemConfig::Instance()->ShowLanguageSelector) {
            $idiomas = Mensaje::ObtenerIdiomas();
            $productos = Mensaje::ObtenerProductos();

            if (isset($idiomas) && count($idiomas) > 0) {
                if (isset($GLOBALS['environment']) && $GLOBALS['environment'] == "desarrollo") {
        ?>
        $('.modal-header.translate').prepend('<div><button class="btn btn-default toggle-message-edit pull-right">Translate</button></div>');
        <?php
                }
            }
        }
        ?>

        ActivaEventosTranslate();
        // $('#toggle-message-edit').click(function () {
        //     // Agregar el evento click as los elementos en pantalla (que no lo tengan ya)
        //     $('.message-edit.new').click(function() {
        //         event.preventDefault();
        //         event.stopPropagation();
        //         EditarMensaje($(this));
        //     });
        //     // Quitar la clase "new" para no agregarle los eventos en posteriores clics del boton Translate
        //     $('.message-edit.new').removeClass('new');

        //     $('.message-edit').toggle();
        // });

        $('.mnuitem-cambiar-idioma').click(function () {
            var idioma = $(this).data('idioma');

            CambiarIdioma(idioma);

            // Cambiar el menu de idioma seleccionado
            $('.mnuitem-idioma').removeClass('active');
            $('.mnuitem-idioma[data-idioma="' + idioma + '"]').addClass('active');
        });
    });

//# sourceURL=localization.js
</script>