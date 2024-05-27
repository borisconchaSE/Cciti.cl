<?php

use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldContent;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldDate;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\FormOnChangeEvent;
use Intouch\Framework\Widget\Action;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\Action\ActionTargetEnum;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Text;


$AppName        =   SystemConfig::Instance()->ApplicationName ?: "App";
$Usuario        =   Session::Instance()->usuario;
$display        =   new Display();

## ----------------------------------------------------------------------------------------------
## CONSTRUIMOS EL HEADER DE LA PAGINA DE sTOCK
## ----------------------------------------------------------------------------------------------
?>

@@Layout(authenticated)
<!-- TITULO PAGINA -->
<div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Mantenedor de información TI</h4>
            </div>
        </div>
    </div>
<?php 

## -----------------------------------------------------------------------------------------------------




$display->AddButton(
    new FormButton(
        Key         :   'btnFiltrar',
        FormKey     :   'frmVerListas',
        ButtonStyle :   ButtonStyleEnum::BUTTON_SECONDARY,
        Child       :   new Text('Filtrar'),
        Styles      :   [   
            ["margin-right", "15px"]
        ] , 
        Events      :   [
            new FormButtonOnClickEvent()
        ]
    )
) ;

$display->AddFormFromObject( 
    formKey         :   'frmVerListas',
    object          :   (object) [
        'FechaDesde'          =>   null,
        'IdTipoServicio'      =>   null,
        'IdUsuario'           =>   null,
        'IdEstadoLista'       =>   null,
        'contactospendientes' =>   null,
    ], 
    keyFieldName    :   'IdUsuario',
    StepByStep      :   False,
    rowGroups       :   [
        new FormRowGroup(
            Key     :   'frg-VerListas', 
            Rows: [
                [  
                    new FormRowFieldDate(
                        PropertyName    :   'FechaDesde',
                        FieldType       :   FormRowFieldTypeEnum::INPUT_DATE,
                        Label           :   'Fecha',
                        Placeholder     :   'Ingrese fecha',
                        Required        :   false,
                        Colspan         :   2, 
                        Events          :   [
                            new FormOnChangeEvent()
                        ]
                    ), 
                    new FormRowFieldSelect(
                        PropertyName: 'IdTipoServicio',
                        Label: 'Área',
                        Colspan: 3,
                        Required: true,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : $TipoServicios,
                            Key             : 'Codigo',
                            Description     : 'Descripcion',
                            MultipleSelection: true,
                            SelectedValue   : -1,
                            DisplaySearch   : true
                        ), 
                    ),  
                    new FormRowFieldSelect(
                        PropertyName: 'IdUsuario',
                        Label: 'Responsable',
                        Colspan: 3,
                        Required: true,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : $Responsables,
                            Key             : 'IdUsuario',
                            Description     : 'Nombre',
                            MultipleSelection: true,
                            SelectedValue   : -1,
                            DisplaySearch   : true
                        ), 
                    ),  
                    
                ],
                [
                    new FormRowFieldSelect(
                        PropertyName: 'IdEstadoLista',
                        Label: 'Estados de las listas',
                        Colspan: 2,
                        Required: true,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : $EstadoLista,
                            Key             : 'IdEstadoLista',
                            Description     : 'Descripcion',
                            MultipleSelection: true,
                            SelectedValue   : -1,
                            DisplaySearch   : true
                        ), 
                    ),  
                    new FormRowFieldContent(
                        Colspan :   3,
                        Content :   new Text('')
                    ), 
                    new FormRowFieldSelect(
                        PropertyName: 'contactospendientes',
                        Label: 'Contactos pendientes',
                        Colspan: 2,
                        Required: false,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values          : [
                                (object)    [
                                    "Idpendientes"   =>   0,
                                    "Descripcion"   =>   "NO"
                                ],
                                (object)    [
                                    "Idpendientes"   =>   1,
                                    "Descripcion"   =>   "SI"
                                ]
                            ],
                            Key             : 'Idpendientes',
                            Description     : 'Descripcion',
                            SelectedValue   : 1,
                            DisplaySearch   : false
                        ),
                        Events      :   [new FormOnChangeEvent()]
                    ),
                ]  
            ]
        )     
    ]

);
 

$content    =    new Container(
    Classes:['container-fluid col-md-12'],
    Children:[
        new Html('<br>'),
        new Container(
            Classes:['grp-list-container'],
            Children:[
                $display->Widgets()['frmVerListas'], 
                new Container(
                    Classes     :   ['content-pull-right'],
                    Children    :   [
                        $display->Widgets()['btnAplicarFiltro'],
                        $display->Widgets()['btnLimpiarFiltro']
                    ]
                ) ,
            ]
        ),

        new Container(
            Classes     :   ['content-pull-right'],
            Children: [
                new Action(
                    Key             :   'btnNuevaLista', 
                    Classes         :   ["btn btn-primary no-decoration"],
                    Child           :   new Html('<i class="bx bx-list-plus"></i> Nueva Lista'),  
                    Action          :   '/listas/nueva',
                    ActionTarget    :   ActionTargetEnum::TARGET_BLANK
                )
            ]
        ) , 
        new Html('<h4 class="mb-sm-0 font-size-14">Listas</h4> <br>'),
        new Container(
            Key     :   'container-lista-filter',
            Children: [

            ]
        )
    ]
);

## DIBUJAMOS LA TABLA COMO TAL
$content->Draw();


## DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:true);


?>

@@RenderBundle(stockJS)

