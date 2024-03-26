class OpcionesStockSvc extends Service {

    constructor() {
      super('Stock');
    }

    GetbyEmpresa(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetbyEmpresa', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GetByDepto(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetByDepto', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GetByUbi(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetByUbi', 'post', entity, onSuccessCallback, onErrorCallback);
    }

}

function OpcionesStockSvc_invoke() {
    return new OpcionesStockSvc();
}

