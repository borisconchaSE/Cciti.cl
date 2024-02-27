<?php

use Application\BLL\DataTransferObjects\Core\TipoUsuarioDto;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
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

## TIPO DE USUARIOS
$TipoUsuarios = [
    new TipoUsuarioDto(
        IdTipoUsuario   :   -1,
        Descripcion     :   'Sin Seleccionar',
        Orden           :   0
    )
];

if(!empty($data->TipoUsuario)){

    $TipoUsuarios   = array_merge($TipoUsuarios,$data->TipoUsuario->Values);

    $TipoUsuarios   =   new GenericCollection(
        DtoName     :   TipoUsuarioDto::class,
        Key         :   'IdTipoUsuario',
        Values      :   $TipoUsuarios
    ) ;
}

$TipoUsuarios->OrderBy('Orden DESC');

## JEFATURA
$UsuarioJefatura = [
    new UsuarioDto(
        IdUsuario       :   -1,
        Nombre          :   'Sin jefatura',
    )
];

if(!empty($data->ListaUsuarios)){

    $UsuarioJefatura   = array_merge($UsuarioJefatura,$data->ListaUsuarios->Values);

    $UsuarioJefatura   =   new GenericCollection(
        DtoName     :   UsuarioDto::class,
        Key         :   'IdUsuario',
        Values      :   $UsuarioJefatura
    ) ;
}
## -------------------------------------------------------------------------------

## VALIDAMOS SI EXISTE O NO LA INFORMACIÓN DEL USUARIO
if($data->Usuario != null){

    ## AGREGAMOS EL BTN QUE NOS PERMITE GUARDAR LOS CAMBIOS
    $display->AddButton(
        new FormButton(
            Key             :   'btnGuardarCambiosUsuario',
            FormKey         :   'frmEditarUsuario',
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
        formKey         :   'frmEditarUsuario',
        object          :   $data->Usuario,
        keyFieldName    :   'IdUsuario',
        rowGroups       :   [ 
            new FormRowGroup(
                Key: 'frg-informaciónUsuarios', 
                Rows: [
                    [ 
                        new FormRowFieldHidden(
                            PropertyName    :   'IdUsuario',
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Nombre',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Nombre Usuario',
                            Required        :   true,
                            Colspan         :   4
                        ), 
                        new FormRowFieldText(
                            PropertyName    :   'Cargo',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Cargo Usuario',
                            Required        :   true,
                            Colspan         :   4
                        ),
                        new FormRowFieldText(
                            PropertyName    :   'Sigla',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Sigla',
                            Required        :   true,
                            Colspan         :   4,
                            Events          :   [
                                new FormOnChangeEvent()
                            ]
                        ), 
                    ],
                    [
                        new FormRowFieldText(
                            PropertyName    :   'LoginName',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_TEXT,
                            Label           :   'Email Usuario',
                            Disabled        :   true,
                            Required        :   true,
                            Colspan         :   6
                        ), 
                        new FormRowFieldText(
                            PropertyName    :   'Password_d',
                            FieldType       :   FormRowFieldTypeEnum::INPUT_PASSWORD,
                            Label           :   'Contraseña',
                            Disabled        :   true,
                            Required        :   false,
                            Colspan         :   6
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
                            PropertyName: 'IdJefeDirecto',
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
        $display->Widgets()['frmEditarUsuario']
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
    Title               : 'Editar Usuario',
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