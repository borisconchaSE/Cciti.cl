class BotonesStockSvc extends Service {

    constructor() {
      super('Stock');
    }

    PopupEditarStock(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupEditarStock', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    PopupAgregarStock(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('PopupAgregarStock', 'post', entity, onSuccessCallback, onErrorCallback);
    }

    GuardarStockNuevo(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
        this.Call('GuardarStockNuevo', 'post', entity, onSuccessCallback, onErrorCallback);
    }
    
    // CambiarParametrosCompra(entity = {}, onSuccessCallback = null, onErrorCallback = null) {
    //     this.Call('CambiarParametrosCompra', 'post', entity, onSuccessCallback, onErrorCallback);
    // }

}

function BotonesStockSvc_invoke() {
    return new BotonesStockSvc();
}