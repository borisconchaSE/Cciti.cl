class PreEntregaSvc extends Service {

    constructor() {
      super('stock/preentrega');
    }

    ObtenerSiguienteHoraAgendamiento(
        idAreaPreEntrega, onSuccessCallback = null, onErrorCallback = null
    ) {

        this.Call(
            'ObtenerSiguienteHoraAgendamiento', 
            'post', 
            {
                IdAreaPreEntrega  : idAreaPreEntrega,
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando la siguiente hora disponible.'});
    }

    BuscarSolicitudes(

        rutCliente, idTipoAuto, 
        codigoVehiculo, numVenta,
        fechaDesde, fechaHasta,
        idAreaPreEntrega,
        marca, modelo, idTipoEstadoSolicitudPreEntrega,
        onSuccessCallback = null, onErrorCallback = null) {

        if (fechaDesde == '' || !fechaDesde) {
            fechaDesde = '-1';
        }

        if (fechaHasta == '' || !fechaHasta) {
            fechaHasta = '-1';
        }

        this.Call(
            'ConsultaSolicitud', 
            'post', 
            {
                RutCliente                      : rutCliente,
                IdTipoAuto                      : idTipoAuto,
                CodigoVehiculo                  : codigoVehiculo,
                NumVenta                        : numVenta,
                FechaDesde                      : fechaDesde,
                FechaHasta                      : fechaHasta,
                Marca                           : marca,
                Modelo                          : modelo,
                IdAreaPreEntrega                : idAreaPreEntrega,
                IdTipoEstadoSolicitudPreEntrega : idTipoEstadoSolicitudPreEntrega
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando Solicitudes de Pre-entrega'});
    }

    BuscarSolicitudesAprobar(
        fechaDesde, fechaHasta, idAreaPreEntrega,
        onSuccessCallback = null, onErrorCallback = null) {

        if (fechaDesde == '' || !fechaDesde) {
            fechaDesde = '-1';
        }

        if (fechaHasta == '' || !fechaHasta) {
            fechaHasta = '-1';
        }

        this.Call(
            'BuscarSolicitudesAprobar', 
            'post', 
            {
                FechaDesde          : fechaDesde,
                FechaHasta          : fechaHasta,
                IdAreaPreEntrega    : idAreaPreEntrega
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando Solicitudes de Pre-entrega'});
    }

    BuscarPreEntregas(
        fechaDesde, fechaHasta,
        marca, modelo, idAreaPreEntrega, idTipoEstadoPreEntega,
        onSuccessCallback = null, onErrorCallback = null) {

        if (fechaDesde == '' || !fechaDesde) {
            fechaDesde = '-1';
        }

        if (fechaHasta == '' || !fechaHasta) {
            fechaHasta = '-1';
        }

        this.Call(
            'BuscarPreEntregas', 
            'post', 
            {
                FechaDesde              : fechaDesde,
                FechaHasta              : fechaHasta,
                Marca                   : marca,
                Modelo                  : modelo,
                IdAreaPreEntrega        : idAreaPreEntrega,
                IdTipoEstadoPreEntrega  : idTipoEstadoPreEntega
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando Pre-entregas'});
    }

    BuscarPreEntregasTerminadas(
        fechaDesde, fechaHasta,
        marca, modelo, idAreaPreEntrega,
        onSuccessCallback = null, onErrorCallback = null) {

        if (fechaDesde == '' || !fechaDesde) {
            fechaDesde = '-1';
        }

        if (fechaHasta == '' || !fechaHasta) {
            fechaHasta = '-1';
        }

        this.Call(
            'BuscarPreEntregasTerminadas', 
            'post', 
            {
                FechaDesde              : fechaDesde,
                FechaHasta              : fechaHasta,
                Marca                   : marca,
                Modelo                  : modelo,
                IdAreaPreEntrega        : idAreaPreEntrega,
            }, 
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Buscando Pre-entregas'});
    }

    CambioEstadoCheckListItem(
        id, estado,
        onSuccessCallback = null, onErrorCallback = null) {
            this.Call(
                'CambioEstadoCheckListItem',
                'post',
                {
                    IdItem             : id,
                    IdEstado        : estado
                },
                onSuccessCallback, 
                onErrorCallback);
        }

    GuardarNuevaSolicitud(
        fechaAgenda, idAreaPreEntrega, TipoAuto, idNotaVenta, idAreaPreEntregaHorario, 
        onSuccessCallback = null, onErrorCallback = null) {
        this.Call(
            'GuardarNuevaSolicitud',
            'post',
            {
                FechaAgenda             : fechaAgenda,
                IdAreaPreEntrega        : idAreaPreEntrega,
                TipoAuto              : TipoAuto,
                IdAreaPreEntregaHorario : idAreaPreEntregaHorario,
                IdNotaVenta             : idNotaVenta
            },
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Ingresando la Solicitud'});
    }

    /*
    ModificarSolicitud(editarSolicitudData, onSuccessCallback = null, onErrorCallback = null){
        this.Call(
            'ModificarSolicitud',
            'post',
            editarSolicitudData,
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Modificando la Solicitud'});
    }
    */

    EliminarSolicitudPreEntrega(idSolicitud, onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'EliminarSolicitudPreEntrega', 
            'post', 
            {
                IdSolicitud: JSON.stringify(idSolicitud)
            }, 
            onSuccessCallback, onErrorCallback,
            { title: 'Por favor espere...', text: 'Eliminando la Solicitud'}); 
                
    }
    

    RechazarSolicitud(idSolicitud, onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'RechazarSolicitudPreEntrega', 
            'post', 
            {
                IdSolicitud: JSON.stringify(idSolicitud)
            }, 
            onSuccessCallback, onErrorCallback,
            { title: 'Por favor espere...', text: 'Rechazando la Solicitud'}); 
                
    }
    
    
    RechazarSolicitudPreEntregaTerminada(idPreEntrega, onSuccessCallback = null, onErrorCallback = null) {

        this.Call(
            'RechazarSolicitudPreEntregaTerminada', 
            'post', 
            {
                IdPreEntrega: idPreEntrega
            }, 
            onSuccessCallback, onErrorCallback,
            { title: 'Por favor espere...', text: 'Rechazando la Solicitud'}); 
                
    }
    
    PopUpCheckList(idPreEntrega, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpCheckList' ,
            'post', 
            {
                IdPreEntrega: idPreEntrega
            },  
            onSuccessCallback, 
            onErrorCallback);
    }
    
    PopUpCheckListHistorial(idPreEntregaCheckListItem, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpCheckListHistorial' ,
            'post', 
            {
                IdPreEntregaCheckListItem: idPreEntregaCheckListItem
            },  
            onSuccessCallback, 
            onErrorCallback);
    }

    PopUpPreEntrega(nVenta, tipoAuto, idVehiculo, onSuccessCallback, onErrorCallback) {
        this.Call(
            'PopUpPreEntrega' ,
            'post', 
            {
                IdNotaVenta: nVenta,
                TipoAuto: tipoAuto,
                IdVehiculo: idVehiculo
            },  
            onSuccessCallback, 
            onErrorCallback);
    }

    AprobarSolicitud(idSolicitudPreEntrega, onSuccessCallback, onErrorCallback) {
        this.Call(
            'AprobarSolicitud' ,
            'post', 
            {
                IdSolicitudPreEntrega: idSolicitudPreEntrega
            },
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Aprobando la Solicitud'});
    }

    
    FinalizarPreEntrega(idPreEntrega, onSuccessCallback, onErrorCallback) {
        this.Call(
            'FinalizarPreEntrega' ,
            'post', 
            {
                IdPreEntrega: idPreEntrega
            },
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Finalizando la Pre-Entrega'});
    }
    
    FinalizarPreEntregaTerminada(idPreEntrega, onSuccessCallback, onErrorCallback) {
        this.Call(
            'FinalizarPreEntregaTerminada' ,
            'post', 
            {
                IdPreEntrega: idPreEntrega
            },
            onSuccessCallback, 
            onErrorCallback,
            { title: 'Por favor espere...', text: 'Aprobando la Pre-Entrega'});
    }
    

    

}

function PreEntregaSvc_invoke() {
    return new PreEntregaSvc();
}

