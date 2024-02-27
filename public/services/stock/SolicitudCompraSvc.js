class SolicitudCompraSvc extends Service {

    constructor() {
      super('stock/solicitudcompra');
    }

    BuscarSolicitudes(
        agnoDesde, 
        codigoCliente,
        idMarca,
        idModelo,
        idVersion,
        idColor,
        idUbicacion,
        IdTipoEstadoSolicitud,
        idVehiculoNuevo,

        onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'ConsultaSolicitud', 
            'post', 
            {
                AgnoDesde: agnoDesde,
                CodigoCliente: codigoCliente,
                IdMarca : idMarca,
                IdModelo : idModelo,
                IdVersion : idVersion, 
                IdColor : idColor,
                IdUbicacion : idUbicacion,
                IdTipoEstadoSolicitud : IdTipoEstadoSolicitud,
                IdVehiculoNuevo : idVehiculoNuevo
                
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando Solicitudes'});
    }

    InsertarSolicitud(nuevaSolicitudData, onSuccessCallback = null, onErrorCallback = null){
        this.Call(
            'InsertarSolicitud',
            'post',
            nuevaSolicitudData,
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Ingresando la Solicitud'});
    }

    ModificarSolicitud(editarSolicitudData, onSuccessCallback = null, onErrorCallback = null){
        this.Call(
            'ModificarSolicitud',
            'post',
            editarSolicitudData,
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Modificando la Solicitud'});
    }

    EliminarSolicitud(idSolicitud, onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'EliminarSolicitudCompra', 
            'post', 
            {
                IdSolicitud: JSON.stringify(idSolicitud)
            }, 
            onSuccessCallback, onErrorCallback,
            { title: 'Por favor espere...', text: 'Eliminando la Solicitud'}); 
                
    }

    RechazarSolicitud(idSolicitud, onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'RechazarSolicitudCompra', 
            'post', 
            {
                IdSolicitud: JSON.stringify(idSolicitud)
            }, 
            onSuccessCallback, onErrorCallback,
            { title: 'Por favor espere...', text: 'Rechazando la Solicitud'}); 
                
    }
    
    PopUpAprobarSolicitud(idSolicitud, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpAprobarSolicitud' ,
            'post', 
            {
                IdSolicitud: idSolicitud
            },  
            onSuccessCallback, 
            onErrorCallback);
    }
    
    AprobarSolicitud(idSolicitud, codigoInterno, onSuccessCallback, onErrorCallback) {
        this.Call(
            'AprobarSolicitud' ,
            'post', 
            {
                IdSolicitud: idSolicitud,
                CodigoInterno: codigoInterno
            },
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Aprobando la Solicitud'});
    }

}

function SolicitudCompraSvc_invoke() {
    return new SolicitudCompraSvc();
}

