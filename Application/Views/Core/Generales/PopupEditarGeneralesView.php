<?php

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\estadoFCDto;
use Application\BLL\DataTransferObjects\Core\estadoOCDto;
use Application\BLL\DataTransferObjects\Core\estadosactivosDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\BLL\DataTransferObjects\Core\modeloDto;
use Application\BLL\DataTransferObjects\Core\proveedorDto;
use Application\BLL\DataTransferObjects\Core\tipoproductoDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldDate;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\FormOnChangeEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\PopUpContent;
use Intouch\Framework\Widget\Text;
use Karriere\JsonDecoder\Property;

$BodyContent     =   new Container(
    Classes     :   ['alert alert-danger label-arrow'],
    Attributes  :   ['role','alert'],
    Children    : [
        new Text('Usted no puede visualizar este usuario o no tiene permisos sobre el')
    ]
);

## INSTANCIAMOS EL DISPLAY QuE NOS PERMITE GENERAR GRAFICOS, TABLAS y FORMULARIOS
$display        =   new Display();



## -------------------------------------------------------------------------------
## GENERAMOS LOS VALORES POR DEFECTO DE LOS INPUTS
## -------------------------------------------------------------------------------
## -------------------------------------------------------------------------------
$Empresa = [
    new empresaDto(
        IdEmpresa           :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->Proveedor->Values)){

    $ProRut        =   $data->Proveedor->Values;
    $ProRut        =   $ProRut[0]->Rut;

}

if(!empty($data->DatosEmpresa)){

    $Empresa   = array_merge($Empresa,$data->DatosEmpresa->Values);

    $Empresa   =   new GenericCollection(
        DtoName     :   empresaDto::class,
        Key         :   'IdEmpresa',
        Values      :   $Empresa
    ) ;
}

$Marca = [
    new marcaDto(
        idMarca   :   -1,
        Descripcion     :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosMarca)){

    $Marca   = array_merge($Marca,$data->DatosMarca->Values);

    $Marca   =   new GenericCollection(
        DtoName     :   marcaDto::class,
        Key         :   'idMarca',
        Values      :   $Marca
    ) ;
}
 
$Modelo = [
    new modeloDto(
        idMarca   :   -1,
        Descripcion     :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosModelo)){

    $Modelo   = array_merge($Modelo,$data->DatosModelo->Values);

    $Modelo   =   new GenericCollection(
        DtoName     :   modeloDto::class,
        Key         :   'idModelo',
        Values      :   $Modelo
    ) ;
}

$Proveedor = [
    new proveedorDto(
        idProveedor         :   -1,
        Nombre              :   'Sin Seleccionar',
        Rut                 :   3
    )
];

if(!empty($data->DatosProveedor)){

    $Proveedor   = array_merge($Proveedor,$data->DatosProveedor->Values);

    $Proveedor   =   new GenericCollection(
        DtoName     :   proveedorDto::class,
        Key         :   'idProveedor',
        Values      :   $Proveedor
    ) ;
}

$EstadoOC = [
    new estadoOCDto(
        idEstado_oc         :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosOC)){

    $EstadoOC   = array_merge($EstadoOC,$data->DatosOC->Values);

    $EstadoOC   =   new GenericCollection(
        DtoName     :   estadoOCDto::class,
        Key         :   'idEstado_oc',
        Values      :   $EstadoOC
    ) ;
}

$EstadoFC = [
    new estadoFCDto(
        idEstado_FC         :   -1,
        Descripcion         :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosFC)){

    $EstadoFC   = array_merge($EstadoFC,$data->DatosFC->Values);

    $EstadoFC   =   new GenericCollection(
        DtoName     :   estadoFCDto::class,
        Key         :   'idEstado_FC',
        Values      :   $EstadoFC
    ) ;
}

$TipoProducto = [
    new tipoproductoDto(
        idTipoProducto              :   -1,
        DescripcionProducto          :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosTipo)){

    $TipoProducto   = array_merge($TipoProducto,$data->DatosTipo->Values);

    $TipoProducto   =   new GenericCollection(
        DtoName     :   tipoproductoDto::class,
        Key         :   'idTipoProducto',
        Values      :   $TipoProducto
    ) ;
}

$EstadoActivo = [
    new estadosactivosDto(
        IdEstadoActivo                  :   -1,
        DescripcionActivo               :   'Sin Seleccionar'
    )
];

if(!empty($data->DatosActivo)){

    $EstadoActivo   = array_merge($EstadoActivo,$data->DatosActivo->Values);

    $EstadoActivo   =   new GenericCollection(
        DtoName     :   estadosactivosDto::class,
        Key         :   'IdEstadoActivo',
        Values      :   $EstadoActivo
    ) ;
}

$EmpresaData        =   $data->Compra->IdEmpresa;
$ProveedorData      =   $data->Compra->idProveedor;
$EstadoOCData       =   $data->Compra->idEstado_oc;
$EstadoFCData       =   $data->Compra->idEstado_FC;
$TipoProductoData   =   $data->Compra->idTipoProducto;
$EstadoActivoData   =   $data->Compra->IdEstadoActivo;
$FechaData          =   $data->Compra->Fecha_compra;
$FechaData          =   date("d-m-Y", strtotime($FechaData));
$Rproveedor         =   $data->Proveedor->Rut;

## VALIDAMOS SI EXISTE O NO LA INFORMACIÓN
if($data->Compra != null){

    ## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
    $display->AddButton(
        new FormButton(
            Key             :   'btnGuardarCambiosCompra',
            FormKey         :   'frmEditarCompra',
            Child           :   new Text('Guardar Cambios'),
            ButtonStyle     :     ButtonStyleEnum::BUTTON_SOFT_SUCCESS,
            Events          :   [
                new FormButtonOnClickEvent()
            ]
        )
    );

    ## EN CASO DE QUE LOS DATOS VENGA VACIO
    ## IMPRIMIMOS UN ERROR EN PANTALLA 
   $display->AddFormFromObject( 
        formKey         :   'frmEditarCompra',
        object          :   (object)[ 
            "idO_C"             =>  $data->Compra->idO_C,
            "Fecha_compra"      =>  $FechaData,
            "IdEmpresa"         =>  $EmpresaData,
            "idProveedor"       =>  $ProveedorData,
            "Proveedor"         =>  $Rproveedor,
            "Descripcion"       =>  $data->Compra->Descripcion,
            "Orden_compra"      =>  $data->Compra->Orden_compra,
            "Factura_compra"    =>  $data->Compra->Factura_compra,
            "Cantidad"          =>  $data->Compra->Cantidad,
            "Precio_total"      =>  $data->Compra->Precio_total,
            "tipo"              =>  $TipoProductoData,
            "IdEstadoActivo"    =>  $EstadoActivoData,
            "idEstado_oc"       =>  $EstadoOCData,
            "idEstado_FC"       =>  $EstadoFCData

         ],
        keyFieldName    :   'idO_C',
        rowGroups       :   [ 
            new FormRowGroup(
                Key: 'frg-informaciónCompra', 
                Rows: [
                    [
                        new FormRowFieldHidden(
                            PropertyName    :   'idO_C',
                        ),
                        new FormRowFieldDate(
                            PropertyName    :   'Fecha_compra',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_DATE,
                            Label           :   'Fecha Compra',
                            Placeholder     :   $FechaData,    
                            Required        :   true,
                            Colspan         :   4, 
                            Events          :   [
                                new FormOnChangeEvent()
                            ]
                        ),
                        new FormRowFieldSelect(
                            PropertyName    :   'IdEmpresa',
                            Label           :   'Empresa',
                            Colspan         :   4,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Empresa,
                                Key             : 'IdEmpresa',
                                Description     : 'Descripcion',
                                SelectedValue   : $EmpresaData,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idProveedor',
                            Label: 'Proveedor',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Proveedor,
                                Key             : 'idProveedor',
                                Description     : 'Nombre',
                                SelectedValue   : $ProveedorData,
                                DisplaySearch   : true
                            ),
                            Events      :   [new FormOnChangeEvent()]  
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Proveedor',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Rut Proveedor',
                            Required        :   true,
                            Colspan         :   4,
                            Disabled        :   true,
                            Events          :   [new FormOnChangeEvent()]
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Descripcion',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Descripcion',
                            Required        :   true,
                            Colspan         :   4
                        ),
                    ],
                    [
                        new FormRowFieldText(
                            PropertyName    :   'Orden_compra',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Orden Compra',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Factura_compra',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Factura Compra',
                            Required        :   true,
                            Colspan         :   4,
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Cantidad',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Cantidad',
                            Required        :   true,
                            Colspan         :   4,
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Precio_total',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Precio Total',
                            Required        :   true,
                            Colspan         :   4,
                        ),
                    ],
                    [
                        new FormRowFieldSelect(
                            PropertyName: 'tipo',
                            Label: 'Tipo de activo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $TipoProducto,
                                Key             : 'idTipoProducto',
                                Description     : 'DescripcionProducto',
                                SelectedValue   : $TipoProductoData,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'IdEstadoActivo',
                            Label: 'Estado del Activo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $EstadoActivo,
                                Key             : 'IdEstadoActivo',
                                Description     : 'DescripcionActivo',
                                SelectedValue   : $EstadoActivoData,
                                DisplaySearch   : true
                            ),
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idEstado_oc',
                            Label: 'Estado OC',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $EstadoOC,
                                Key             : 'idEstado_oc',
                                Description     : 'Descripcion',
                                SelectedValue   : $EstadoOCData,
                                DisplaySearch   : true
                            ), 
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idEstado_FC',
                            Label: 'Estado FC',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $EstadoFC,
                                Key             : 'idEstado_FC',
                                Description     : 'Descripcion',
                                SelectedValue   : $EstadoFCData,
                                DisplaySearch   : true
                            ), 
                        ),
                    ]
                ]
            ) 
        ],
        fillData        :   true
   );

   $BodyContent         =   new Container(
    Children:[
        $display->Widgets()['frmEditarCompra']
    ]
   ) ;

 


} 



$content = new Container(
    Classes: ['view-content'],
    Styles: [],
    Children: [
        $BodyContent
    ]
);

$popUp = new PopUpContent(
    Key                 : 'PopupNuevo', 
    Title               : 'Editar Activo',
    DismissButtonText   : 'Cerrar',
    DismissButtonStyle  : ButtonStyleEnum::BUTTON_SOFT_PRIMARY,
    SubTitle            : '',
    Buttons             :   [
        $display->Widgets()['btnGuardarCambiosCompra']
    ],
    Content             : $content
);

$popUp->Draw();

$display->DrawScripts(addLoadEvent:false);