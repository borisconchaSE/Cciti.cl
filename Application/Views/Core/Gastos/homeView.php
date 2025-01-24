<?php

use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayEvents\ButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\ScriptDirect;
use Intouch\Framework\Widget\Text;

$AppName        =   SystemConfig::Instance()->ApplicationName ?: "App";
$Usuario        =   Session::Instance()->usuario;
$display        =   new Display();

## ----------------------------------------------------------------------------------------------
## CONSTRUIMOS EL HEADER DE LA PAGINA DE ADMINISTRACIÓN
## ----------------------------------------------------------------------------------------------
?>

@@Layout(authenticated)
<!-- start page title -->
<div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Gastos</h4>
            </div>
        </div>
    </div>
<!-- end page title --> 
<?php 


## ----------------------------------------------------------------------------------------------
## UNA VEZ DEFINIDO, PROCEDEMOS A GENERAR LA VISUALIZACIÓN DE LOS COMPONENTES DE LA TABLA
## ----------------------------------------------------------------------------------------------
$display->AddButton(
    new Button(
        Key             :   'btnNuevaCompra',
        Child           :   new FaIconText('fa-plus-circle','Agregar Producto'),
        Classes         :   ['pull-right'],
        ButtonStyle     :   ButtonStyleEnum::BUTTON_SOFT_INFO,
        Events          :   [
            new ButtonOnClickEvent()
        ]
    )
) ;


$CantidadCompras    =   !empty($data['ListaCompras']) ? $data['ListaCompras']->count() : 0;

$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Cantidad Total<span class="text-muted fw-normal ms-2">('.$CantidadCompras.')</span></h5>')
                    ]
                )
            ]
                    ),
        new Container(
            Classes:['col-md-6'],
            Children:[
                $display->Widgets()['btnNuevaCompra'],
            ]
        ), 
          
    ]
); 
 





## COMENZAMOS A DIBUJAR LA TABLA CON SUS COLUMNAS

if(empty($data['ListaCompras'])){
    (
        new Container(
            Children:[
                new Container(
                    Classes:['col-md-6'],
                    Styles:[["float","right"],["margin","5px"]],
                    Children:[
                        new Html('
    
                            <a role="button" class="pull-right btn btn-soft-info" style="position: relative;  cursor: pointer; " id="btnNuevaCompra" name="btnNuevaCompra">
                                <i class="fa fa-plus-circle" style="margin-right: 8px; "></i><span>Agregar Tonner</span>
                                <div class="hide badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="btnNuevaCompraBadge">
                                <span class="badge badge-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="btnNuevaCompraBadgeContent">
                                </span>
                                </div>
                            </a>
    
                        ')
                    ],
                ),
                new Html('<br>'),
                new Html('<br>'),
                new Container(
                    Classes:['col-md-12'],
                    Styles:[["text-align","center"]],
                    Children:[
                        new Html('<br>'),
                        new Html('<br>'),
                        new FaIcon('fas fa-smile-beam',Styles: [["font-size","70px"]]),
                        new Html('<br>'),
                        new Html('<br>'),
                        new Text('No tenemos ningun activo'), 
                    ]
                )
            ]
        )
    )->Draw();

    $script     =   new ScriptDirect([
        "
        setTimeout(() => {
            $($('a[id=btnNuevaCompra]')[0]).off('click');
            $($('a[id=btnNuevaCompra]')[0]).attr('onClick','btnNuevaCompra_OnClick(this)');
        }, '500');
        "
    ]);

    $script->Draw();
    
    // return false;

}else
{
    $cellDefinitions    =   [
        new JSTableCell(
            PropertyName: 'Fecha_Compra',
            Label: 'Fecha Compra',
            Colspan: 2,
            BodyClasses :   ["center"]   
        ),
        new JSTableCell(
            PropertyName: 'Nombre_Producto',
            Label: 'Descripcion',
            Colspan: 2,
            BodyClasses :   ["center"]   
        ),
        new JSTableCell(
            PropertyName: 'Orden_Compra',
            Label: 'Orden Compra',
            Colspan: 2,
            BodyClasses :   ["center"]   
        ),
        new JSTableCell(
            PropertyName: 'Factura_Compra',
            Label: 'Factura Compra',
            Colspan: 2,
            BodyClasses :   ["center"]   
        ),
        new JSTableCell(
            PropertyName: 'Precio_Total',
            Label: 'Precio total',
            Colspan: 2,
            BodyClasses :   ["center"]   
        ),
        new JSTableCell(
            PropertyName: 'Tipo',
            Colspan: 2,
            Label: 'Tipo',
            BodyClasses :   ["center"],
        ),
        new JSTableCell(
            PropertyName: 'Proveedor',
            Colspan: 2,
            Label: 'Proveedor',
            BodyClasses :   ["center"],
        ),
        new JSTableCell(
            PropertyName: 'Estado_OC',
            Colspan: 2,
            Label: 'Estado OC',
            BodyClasses :   ["center"],
        ),
        new JSTableCell(
            PropertyName: 'Estado_FC',
            Colspan: 2,
            Label: 'Estado FC',
            BodyClasses :   ["center"],
        ),
        new JSTableCell(
            PropertyName: 'Empresa',
            Colspan: 2,
            Label: 'Empresa',
            BodyClasses :   ["center"],
        ),
    ] ;

    
    $display->AddTableFromCollection(
        tableKey: 'tbListadoCompras',
        RowIdFieldName: 'idO_C',
        RowAttributeNames: ['idO_C'],
        CellDefinitions: $cellDefinitions,
        Data: $data['ListaCompras'],
        Buttons: [
            new JSTableButton(
                Key             :   'btnEditarCompra',
                Child           :   new FaIcon('fa-edit'),
                Classes         :   ['btn-sm'],
                OnClickClass    :   'btnEditarCompra',
                TogglePopUp     :   true,
                ToggleText      :   'Editar',
                ButtonStyle     :   ButtonStyleEnum::BUTTON_SUCCESS,
                Events          :   [ new TableButtonOnClickEvent() ],)
        ],
        TablaSimple: false,
        JSRenderTheTable : true,
        customExcel : new CustomExcelSettingsFilterDto(
            ShowButton      :   true,
            Controller      :   "lista",
            FileName        :   "Lista Gastos",
            Estado          :   null,
            TableKey        :   'tbListadoGastos'
        ),
        CustomDataTable: new DataTableSettingsFilterDto(
            HideAllButtons  : false,
            CustomPdf       : false,
            TableHasButtons : true,
            GroupedButtons  : false,
        )
    );


    $content    =   new Container(
        Classes     :   ['row'],
        Children    :   [ 
            new Container(
                Classes     :   ['col-lg-12'],
                Children    :   [
                    $tableheader,
                    new Card(
                        Classes:['table-responsive'],
                        Children:[
                            new Html('<br>'),
                            $display->Widgets()['tbListadoCompras']
                        ]
                    )
                ]
            )
        ]
    );





    

    ## DIBUJAMOS LA TABLA COMO TAL
    $content->Draw();
}

## DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:true);

?>
@@RenderBundle(gastosJS)
