    document.addEventListener('DOMContentLoaded', function load() {
        if (!window.jQuery) return setTimeout(load, 50);

        //your synchronous or asynchronous jQuery-related code
        try {
            $('.input-group.clockpicker').clockpicker({ 
                autoclose: true
            });
        }
        catch(err) {}

        try {
            $('.input-group.date').datepicker({
                locale: 'es', autoclose: true, format: 'dd-mm-yyyy',language: "es"
            });
        }
        catch(err) {}

        try {
            $('.js-source-states.search-on').select2({
                minimumResultsForSearch: 0
            });
        }
        catch(err) {}

        try {
            $('.js-source-states.search-off').select2({
                minimumResultsForSearch: Infinity
            });
        }
        catch(err) {}

    }, false);