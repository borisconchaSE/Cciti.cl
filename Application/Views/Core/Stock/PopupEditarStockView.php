<?php

use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\BLL\DataTransferObjects\Core\empresaDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
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
use Karriere\JsonDecoder\Property;

$BodyContent     =   new Container(
    Classes     :   ['alert alert-danger label-arrow'],
    Attributes  :   ['role','alert'],
    Children    : [
        new Text('Error al visualizar los datos del producto')
    ]
);

## INSTANCIAMOS EL DISPLAY QuE NOS PERMITE GENERAR GRAFICOS, TABLAS y FORMULARIOS
$display        =   new Display();


if(!empty($data->Stock->tipo)){

    if(($data->Stock->tipo) == "Original" ){
        $Tipo   = "Original";
    }else{
        $Tipo   = "Alternativo";
    }


}

if(!empty($data->Stock->estado_stock)){

    if(($data->Stock->estado_stock) == "En Stock" ){
        $estado   = "En Stock";
    }else{
        $estado   = "Entregado";
    }


}

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

$EmpresaData    =   $data->Stock->IdEmpresa;
$MarcaData      =   $data->Stock->idMarca;
$FechaData      =   $data->Stock->Fecha;
$FechaData      =   date("d-m-Y", strtotime($FechaData));

## VALIDAMOS SI EXISTE O NO LA INFORMACIÓN DEL USUARIO
if($data->Stock != null){

    ## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
    $display->AddButton(
        new FormButton(
            Key             :   'btnGuardarCambiosStock',
            FormKey         :   'frmEditarStock',
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
        formKey         :   'frmEditarStock',
        object          :   $data->Stock,
        keyFieldName    :   'id_stock',
        rowGroups       :   [ 
            new FormRowGroup(
                Key: 'frg-informaciónEditarStock', 
                Rows: [
                    [
                        new FormRowFieldHidden(
                            PropertyName    :   'id_stock',
                        ),
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
                            Placeholder     :   $FechaData,    
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
                                        "tipo"              =>   "Original",
                                        "Descripcion"       =>   "Original"
                                    ],
                                    (object)    [
                                        "tipo"              =>   "Alternativo",
                                        "Descripcion"       =>   "Alternativo"
                                    ]
                                ],
                                Key             : 'tipo',
                                Description     : 'Descripcion',
                                SelectedValue   : $Tipo,
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
                        )
                    ],
                    [
                        new FormRowFieldSelect(
                            PropertyName: 'estado_stock',
                            Label: 'Estado Producto',
                            Colspan: 4,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : [
                                    (object)    [
                                        "tipo"              =>   "En Stock",
                                        "Descripcion"       =>   "En Stock"
                                    ],
                                    (object)    [
                                        "tipo"              =>  "Entregado",
                                        "Descripcion"       =>  "Entregado"
                                    ]
                                ],
                                Key             : 'tipo',
                                Description     : 'Descripcion',
                                SelectedValue   : $estado,
                                DisplaySearch   : true
                            ),
                            Events      :   [new FormOnChangeEvent()]
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
                        )
                    ]
                ]
            ) 
        ],
        fillData        :   true
   );

   $BodyContent         =   new Container(
    Children:[
        $display->Widgets()['frmEditarStock']
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
    Key                 : 'PopupEditarStock', 
    Title               : 'Editar Stock',
    DismissButtonText   : 'Cerrar',
    DismissButtonStyle  : ButtonStyleEnum::BUTTON_SOFT_PRIMARY,
    SubTitle            : '',
    Buttons             :   [
        $display->Widgets()['btnGuardarCambiosStock']
    ],
    Content             : $content
);

$popUp->Draw();

$display->DrawScripts(addLoadEvent:false);