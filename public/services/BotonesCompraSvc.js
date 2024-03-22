class BotonesCompraSvc extends Service {

    constructor() {
      super('Compra');
    }

    PopupEditarCompra(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupEditarCompra', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    PopupAgregarProducto(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupAgregarProducto', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GuardarNuevoProducto(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GuardarNuevoProducto', 'post', entity, onSuccessCallback, onErrorCallback);
    }
    
    CambiarParametrosCompra(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('CambiarParametrosCompra', 'post', entity, onSuccessCallback, onErrorCallback);
    }

}

function BotonesCompraSvc_invoke() {
    return new BotonesCompraSvc();
}