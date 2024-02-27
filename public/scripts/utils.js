var limitemb = 2097152;

$.extend(true, $.fn.dataTable.defaults, {
    'language': {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
});
$.fn.datetimepicker.defaults.format = 'DD-MM-YYYY';
$.fn.datetimepicker.defaults.locale = moment.locale('es');
$.fn.datetimepicker.defaults.ignoreReadonly = true;
var timeToWait = 2000;

function reloadWithTimes(time) {
    setTimeout(function () {
        window.location.reload(1);
    }, time);
}

function formatDDMMYYYY(fecha) {
    return moment(fecha, "YYYY-MM-DD").format('DD-MM-YYYY')
}

function YYYYMMDDhmsTODDMMYYYY(fecha) {
    return moment(fecha, "YYYY-MM-DD HH:mm:ss").format('DD-MM-YYYY')
}

// Create our number formatter.
var moneyFormatter = new Intl.NumberFormat('es-CL', {
    style: 'currency',
    currency: 'CLP',
  
    // These options are needed to round to whole numbers if that's what you want.
    minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
    maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
});

function _mon(ammount) {
    return moneyFormatter.format(ammount);
}
  
