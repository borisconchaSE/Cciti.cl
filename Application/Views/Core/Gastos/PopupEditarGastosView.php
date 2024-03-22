<?php

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\estadoFCDto;
use Application\BLL\DataTransferObjects\Core\estadoOCDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\BLL\DataTransferObjects\Core\modeloDto;
use Application\BLL\DataTransferObjects\Core\proveedorDto;
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

if(!empty($data->Compra->tipo)){

    if(($data->Compra->tipo) == "Original" ){
        $Tipo   = "Original";
    }else{
        $Tipo   = "Alternativo";
    }


}

$EmpresaData    =   $data->Compra->IdEmpresa;
$ProveedorData  =   $data->Compra->idProveedor;
$EstadoOCData   =   $data->Compra->idEstado_oc;
$EstadoFCData   =   $data->Compra->idEstado_FC;
$MarcaData      =   $data->Marca->idMarca;
$ModeloData     =   $data->Modelo->idModelo;
$FechaData      =   $data->Compra->Fecha_compra;
$FechaData      =   date("d-m-Y", strtotime($FechaData));

if($MarcaData == null){
    $MarcaData = -1;
}

if($ModeloData == null){
    $ModeloData = -1;
}

## VALIDAMOS SI EXISTE O NO LA INFORMACIÓN DEL USUARIO
if($data->Compra != null){

    ## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
    $display->AddButton(
        new FormButton(
            Key             :   'btnGuardarCambiosCompra',
            FormKey         :   'frmEditarCompra',
            Child           :   new Text('Guardar Cambios'),
            #Classes         :   ['pull-right', 'wide'],
            ButtonStyle     :     ButtonStyleEnum::BUTTON_SOFT_SUCCESS,
            Events          :   [
                new FormButtonOnClickEvent()
            ]
        )
    );

    ## EN CASO DE QUE LOS DATOS DEL CLIENTE VENGA VACIO
    ## IMPRIMIMOS UN ERROR EN PANTALLA 
   $display->AddFormFromObject( 
        formKey         :   'frmEditarCompra',
        object          :   $data->Compra,
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
                        new FormRowFieldText(
                            PropertyName    :   'Descripcion',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Nombre Producto',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idMarca',
                            Label: 'Marca',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Marca,
                                Key             : 'idMarca',
                                Description     : 'Descripcion',
                                SelectedValue   : $MarcaData,
                                DisplaySearch   : true
                            ), 
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idModelo',
                            Label: 'Modelo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Modelo,
                                Key             : 'idModelo',
                                Description     : 'Descripcion',
                                SelectedValue   : $ModeloData,
                                DisplaySearch   : true
                            ), 
                        ),
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
                    ],
                    [
                        new FormRowFieldText(
                            PropertyName    :   'Precio_U',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Precio Unitario',
                            Required        :   true,
                            Colspan         :   4
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
                        new FormRowFieldSelect(
                            PropertyName: 'tipo',
                            Label: 'Tipo',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : [
                                    (object)    [
                                        "tipo"          =>   "Original",
                                        "Descripcion"   =>   "Original"
                                    ],
                                    (object)    [
                                        "tipo"           =>  "Alternativo",
                                        "Descripcion"   =>   "Alternativo"
                                    ]
                                ],
                                Key             : 'tipo',
                                Description     : 'Descripcion',
                                SelectedValue   : $Tipo,
                                DisplaySearch   : true
                            ),
                            Events      :   [new FormOnChangeEvent()]
                        ),
                    ],
                    [
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
    Title               : 'Editar Compra',
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