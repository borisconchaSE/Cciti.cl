class AdminUsuarioSvc extends Service {

    constructor() {
      super('administracion/cuentas');
    }

    PopupEditarUsuario(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupEditarUsuario', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    PopupNuevoUsuario(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupNuevoUsuario', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GuardarNuevoUsuario(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GuardarNuevoUsuario', 'post', entity, onSuccessCallback, onErrorCallback);
    }
    
    CambiarParametrosUsuario(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('CambiarParametrosUsuario', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    ActivarDesactivarUsuario(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('ActivarDesactivarUsuario', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    

}

function AdminUsuarioSvc_invoke() {
    return new AdminUsuarioSvc();
}

