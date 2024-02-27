class ConsultaStockSvc extends Service {

    constructor() {
      super('stock/consultastock');
    }

    BuscarUsados(
        agnoDesde, agnoHasta, kilometrajeDesde, kilometrajeHasta, precioDesde, precioHasta,
        idVehiculo, idMarca, idModelo, idColor, idTransmision, idTraccion,
        idCombustible, idEstadoCompra, idEstadoVenta,
        onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'ConsultaStockUsados', 
            'post', 
            {
                AgnoDesde: agnoDesde,
                AgnoHasta: agnoHasta,
                KilometrosDesde: kilometrajeDesde,
                KilometrosHasta: kilometrajeHasta,
                PrecioDesde: precioDesde,
                PrecioHasta: precioHasta,
                IdVehiculo: idVehiculo,
                IdMarca: idMarca,
                IdModelo: idModelo,
                IdColor: idColor,
                IdTransmision: idTransmision,
                IdTraccion: idTraccion,
                IdCombustible: idCombustible,
                IdEstadoCompra: idEstadoCompra,
                IdEstadoVenta: idEstadoVenta
            }, 
            onSuccessCallback, 
            onErrorCallback);
    }

    BuscarNuevos(
        agnoDesde, 
        // agnoHasta,
        // precioDesde, precioHasta,
        idEstadoCompra, idEstadoVenta,
        idVehiculo, idMarca,
        idModelo, idVersion,
        idColor,
        //idBodega,
        idUbicacion,
        onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'ConsultaStockNuevos', 
            'post', 
            {
                AgnoDesde: agnoDesde,
                // AgnoHasta: agnoHasta,
                // PrecioDesde: precioDesde,
                // PrecioHasta: precioHasta,
                IdEstadoCompra : idEstadoCompra, 
                IdEstadoVenta : idEstadoVenta,
                IdVehiculo : idVehiculo,
                IdMarca : idMarca,
                IdModelo : idModelo, 
                IdVersion : idVersion,
                IdColor : idColor,
                // IdBodega : idBodega,
                IdUbicacion : idUbicacion
                
            }, 
            onSuccessCallback, 
            onErrorCallback);
    }

    PopUpVehiculoUsado(idVehiculoUsado, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpVehiculoUsado' ,
            'post', 
            {
                IdVehiculoUsado: idVehiculoUsado
            },  
            onSuccessCallback, 
            onErrorCallback);
    }

    PopUpVehiculoNuevo(idVehiculoNuevo, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpVehiculoNuevo' ,
            'post', 
            {
                IdVehiculoNuevo: idVehiculoNuevo
            },  
            onSuccessCallback, 
            onErrorCallback);
    }

}

function ConsultaStockSvc_invoke() {
    return new ConsultaStockSvc();
}

