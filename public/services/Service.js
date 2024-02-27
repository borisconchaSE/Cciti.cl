class Service {

    Api = '/api';
    Controller = '';

    constructor(controller) {
        this.Controller = this.Api + '/' + controller;
    }

    Create(entity, onSuccessCallback = null, onErrorCallback = null) {

        this.Call('create', 'post', entity, onSuccessCallback, onErrorCallback, { title: 'Por favor espere...', text: 'Guardando Información'});

    }

    Update(entity, onSuccessCallback = null, onErrorCallback = null) {
    }

    Delete(entity, onSuccessCallback = null, onErrorCallback = null) {
    }

    Find(entityId, onSuccessCallback = null, onErrorCallback = null) {
    }

    Get(onSuccessCallback = null, onErrorCallback = null) {

        this.Call('get', 'post', null, onSuccessCallback, onErrorCallback, { title: 'Por favor espere...', text: 'Obteniendo Información'});

    }

    GetBy(filter, action, onSuccessCallback = null, onErrorCallback = null) {  
            
        this.Call(action, 'post', filter, onSuccessCallback, onErrorCallback, { title: 'Por favor espere...', text: 'Obteniendo Información'});

    }

    // Realiza la llamada AJAX
    //
    Call(action, method = 'post', entity = null, onSuccessCallback = null, onErrorCallback = null, waitConfig = null) {

        var uri = this.Controller + '/' + action;

        if (waitConfig != null) {
            Swal.fire({
                title: waitConfig.title,
                text: waitConfig.text,
                showConfirmButton: false
            });
        }

        if (entity && entity != null) {

            $.ajax({
                type: method,
                dataType: 'json',
                url: uri,
                data: entity,
                success: function(data) {

                    if (waitConfig != null) {
                        Swal.close();
                    }

                    if (data.ErrorCode > 0) {
                        if (onErrorCallback != null && typeof onErrorCallback === 'function') {
                            onErrorCallback (data.ErrorCode, data.ErrorMessage);
                        }
                    }
                    else {
                        if (onSuccessCallback && typeof onSuccessCallback === 'function') {

                            if (data.ResultType == 'ActionResult' || data.ResultType == 'Action') {
                                onSuccessCallback(data.Result);
                            }
                            else if (data.ResultType == 'ActionView') {
                                onSuccessCallback(data.ViewContent);
                            }
                            else {
                                onSuccessCallback(data.Result);
                            }
                            
                        }
                    }
                },
                error: function(data) {

                    if (waitConfig != null) {
                        Swal.close();
                    }

                    if (onErrorCallback != null && typeof onErrorCallback === 'function') {
                        onErrorCallback(1, 'Ha ocurrido un error inesperado');
                    }
                }
            });
        }
        else {

            $.ajax({
                type: 'post',
                url: uri,
                success: function(data) {

                    if (waitConfig != null) {
                        Swal.close();
                    }

                    if (data.ErrorCode > 0) {
                        if (onErrorCallback != null && typeof onErrorCallback === 'function') {
                            onErrorCallback (data.ErrorCode, data.ErrorMessage);
                        }
                    }
                    else {
                        if (onSuccessCallback && typeof onSuccessCallback === 'function') {
                            if (data.ResultType == 'ActionResult' || data.ResultType == 'Action') {
                                onSuccessCallback(data.Result);
                            }
                            else if (data.ResultType == 'ActionView') {
                                onSuccessCallback(data.ViewContent);
                            }
                            else {
                                onSuccessCallback(data.Result);
                            }
                        }
                    }
                },
                error: function(data) {                    

                    if (waitConfig != null) {
                        Swal.close();
                    }

                    if (onErrorCallback != null && typeof onErrorCallback === 'function') {
                        onErrorCallback(null);
                    }
                }
            });

        }

    }
}