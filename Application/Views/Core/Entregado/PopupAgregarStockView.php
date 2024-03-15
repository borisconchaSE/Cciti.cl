<?php

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldContent;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldDate;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
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

$BodyContent     =   new Container(
    Classes     :   ['alert alert-danger label-arrow'],
    Attributes  :   ['role','alert'],
    Children    : [
        new Text('Usted no tiene permisos para este recurso')
    ]
);

## INSTANCIAMOS EL DISPLAY QuE NOS PERMITE GENERAR GRAFICOS, TABLAS y FORMULARIOS
$display        =   new Display();


## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
$display->AddButton(
    new FormButton(
        Key             :   'btnAgregarNuevoStock',
        FormKey         :   'frmStockNuevo',
        Child           :   new Text('Guardar Cambios'),
        #Classes         :   ['pull-right', 'wide'],
        ButtonStyle     :     ButtonStyleEnum::BUTTON_SOFT_SUCCESS,
        Events          :   [
            new FormButtonOnClickEvent()
        ]
    )
);

## -------------------------------------------------------------------------------
## GENERAMOS LOS VALORES POR DEFECTO DE LOS INPUTS
## -------------------------------------------------------------------------------
## -------------------------------------------------------------------------------
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

$Empresa = [
    new empresaDto(
        IdEmpresa       :   -1,
        Descripcion     :   'Sin Seleccionar'
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



## EN CASO DE QUE LOS DATOS VENGA VACIO
## IMPRIMIMOS UN ERROR EN PANTALLA 
$display->AddFormFromObject( 
    formKey         :   'frmStockNuevo',
    object          :   (object)[  ],
    keyFieldName    :   'id_stock',
    rowGroups       :   [ 
        new FormRowGroup(
            Key: 'frg-informaciónStock', 
            Rows: [
                [
                    new FormRowFieldText(
                        PropertyName    :   'Descripcion',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                        Label           :   'Nombre Producto',
                        Required        :   true,
                        Colspan         :   4
                    ),
                    new FormRowFieldDate(
                        PropertyName    :   'Fecha',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_DATE,
                        Label           :   'Fecha Llegada',
                        Required        :   true,
                        Colspan         :   4, 
                        Events          :   [
                            new FormOnChangeEvent()
                        ]
                    ),
                    new FormRowFieldDate(
                        PropertyName    :   'Fecha_Asignacion',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_DATE,
                        Label           :   'Fecha Entrega',
                        Required        :   true,
                        Colspan         :   4, 
                        Events          :   [
                            new FormOnChangeEvent()
                        ]
                    ),
                    new FormRowFieldSelect(
                        PropertyName: 'tipo',
                        Label: 'Tipo',
                        Colspan: 4,
                        Required: true,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : [
                                (object)    [
                                    "tipo"              =>   0,
                                    "Descripcion"       =>   "Original"
                                ],
                                (object)    [
                                    "tipo"              =>   1,
                                    "Descripcion"       =>   "Alternativo"
                                ]
                            ],
                            Key             : 'tipo',
                            Description     : 'Descripcion',
                            SelectedValue   : 0,
                            DisplaySearch   : true
                        ),
                        Events      :   [new FormOnChangeEvent()]
                    )
                    ],
                [
                    new FormRowFieldText(
                        PropertyName    :   'Cantidad',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                        Label           :   'Cantidad',
                        Required        :   true,
                        Colspan         :   4
                    ),
                    new FormRowFieldText(
                        PropertyName    :   'Precio_Unitario',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                        Label           :   'Precio Unitario',
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
                        PropertyName: 'estado_stock',
                        Label: 'Estado Producto',
                        Colspan: 4,
                        Required: true,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : [
                                (object)    [
                                    "tipo"          =>   0,
                                    "Descripcion"   =>   "En Stock"
                                ],
                                (object)    [
                                    "tipo"           =>   1,
                                    "Descripcion"   =>   "Entregado"
                                ]
                            ],
                            Key             : 'tipo',
                            Description     : 'Descripcion',
                            SelectedValue   : 0,
                            DisplaySearch   : true
                        ),
                        Events      :   [new FormOnChangeEvent()]
                    )
                    ],
                    [
                        new FormRowFieldSelect(
                            PropertyName    :   'IdEmpresa',
                            Label           :   'Empresa',
                            Colspan         :   6,
                            Required        : true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Empresa,
                                Key             : 'IdEmpresa',
                                Description     : 'Descripcion',
                                SelectedValue   : -1,
                                DisplaySearch   : true
                            ), 
                        ),
                        new FormRowFieldSelect(
                            PropertyName: 'idMarca',
                            Label: 'Marca',
                            Colspan: 6,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $Marca,
                                Key             : 'idMarca',
                                Description     : 'Descripcion',
                                SelectedValue   : -1,
                                DisplaySearch   : true
                            ), 
                        )
                        ]
            ]
        ) 
    ],
    fillData        :   false
);

$BodyContent         =   new Container(
    Children:[
        $display->Widgets()['frmStockNuevo']
    ]
) ;


 


$content = new Container(
    Classes: ['view-content'],
    Styles: [],
    Children: [
        $BodyContent
    ]
);

$popUp = new PopUpContent(
    Key                 : 'PopupStockNuevo', 
    Title               : 'Agregar al Stock',
    DismissButtonText   : 'Cerrar',
    DismissButtonStyle  : ButtonStyleEnum::BUTTON_SOFT_PRIMARY,
    SubTitle            : '',
    Buttons             :   [
        $display->Widgets()['btnAgregarNuevoStock']
    ],
    Content             : $content
);

$popUp->Draw();

$display->DrawScripts(addLoadEvent:false);