class OpcionesCompraSvc extends Service {

    constructor() {
      super('Compra');
    }

    GetbyMarca(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetbyMarca', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GetbyProveedor(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetbyProveedor', 'post', entity, onSuccessCallback, onErrorCallback);
    }

}

function OpcionesCompraSvc_invoke() {
    return new OpcionesCompraSvc();
}

