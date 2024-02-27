<?php

use Application\BLL\DataTransferObjects\Core\stockDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
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
        new Text('Usted no puede visualizar este usuario o no tiene permisos sobre el')
    ]
);

## INSTANCIAMOS EL DISPLAY QuE NOS PERMITE GENERAR GRAFICOS, TABLAS y FORMULARIOS
$display        =   new Display();



## -------------------------------------------------------------------------------
## GENERAMOS LOS VALORES POR DEFECTO DE LOS INPUTS
## -------------------------------------------------------------------------------
## -------------------------------------------------------------------------------

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
        formKey         :   'frmEditarStock',
        object          :   $data->id_stock,
        keyFieldName    :   'id_stock',
        rowGroups       :   [ 
            new FormRowGroup(
                Key: 'frg-informaciónStock', 
                Rows: [
                    [
                        new FormRowFieldHidden(
                            PropertyName    :   'id_stock',
                        ), 
                        new FormRowFieldText(
                            PropertyName    :   'Fecha_compra',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Fecha Compra',
                            Required        :   true,
                            Colspan         :   4
                        ), 
                        new FormRowFieldText(
                            PropertyName    :   'Descripcion',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Nombre Producto',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'marca',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Marca',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'modelo',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Modelo',
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
                    ],
                    [
    
                        new FormRowFieldSelect(
                            PropertyName: 'IdTipoUsuario',
                            Label: 'Tipo Usuario',
                            Colspan: 6,
                            Required: true,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $TipoUsuarios,
                                Key             : 'IdTipoUsuario',
                                Description     : 'Descripcion',
                                SelectedValue   : -1,
                                DisplaySearch   : true
                            ), 
                        ),  
                        new FormRowFieldSelect(
                            PropertyName: 'IdUsuario',
                            Label: 'Jefe Directo',
                            Colspan: 6,
                            SelectDefinition: new FormRowFieldSelectDefinition(
                                Values          : $UsuarioJefatura,
                                Key             : 'IdUsuario',
                                Description     : 'Nombre',
                                SelectedValue   : -1,
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
    Key                 : 'PopupNuevo', 
    Title               : 'Editar Stock',
    DismissButtonText   : 'Cerrar',
    DismissButtonStyle  : ButtonStyleEnum::BUTTON_SOFT_PRIMARY,
    SubTitle            : '',
    Buttons             :   [
        $display->Widgets()['btnGuardarCambiosUsuario']
    ],
    Content             : $content
);

$popUp->Draw();

$display->DrawScripts(addLoadEvent:false);