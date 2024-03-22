class OpcionesStockSvc extends Service {

    constructor() {
      super('Stock');
    }

    GetbyEmpresa(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GetbyEmpresa', 'post', entity, onSuccessCallback, onErrorCallback);
    }

}

function OpcionesStockSvc_invoke() {
    return new OpcionesStockSvc();
}

