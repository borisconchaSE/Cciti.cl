<?php

use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\JSTableContent;
use Intouch\Framework\Widget\JSTableScriptFilter;

use function PHPSTORM_META\map;

$AppName        =   SystemConfig::Instance()->ApplicationName ?: "App";
$Usuario        =   Session::Instance()->usuario;
$display        =   new Display();

## ----------------------------------------------------------------------------------------------
## CONSTRUIMOS EL HEADER DE LA PAGINA DE STOCK DE TONNERS ENTREGADOS
## ----------------------------------------------------------------------------------------------
?>

@@Layout(authenticated)
<!-- TITULO DE LA PAGINA -->
<div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Stock de tonners entregados</h4>
            </div>
        </div>
    </div>
<?php 


## ----------------------------------------------------------------------------------------------
## UNA VEZ DEFINIDO, PROCEDEMOS A GENERAR LA VISUALIZACIÓN DE LOS COMPONENTES DE LA TABLA
## ----------------------------------------------------------------------------------------------


// CONTAMOS LA CANTIDAD DE STOCK ENTREGADO QUE EXISTE ACTUALMENTE PARA MOSTRAR
$CantidadStock    =   !empty($data['Stock']) ? $data['Stock']->count() : 0;

//GENERAMOS EL HEADER DE LA TABLA DONDE MOSTRAMOS EL TOTAL DE STOCK
$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Cantidad Total<span class="text-muted fw-normal ms-2">('.$CantidadStock.')</span></h5>')
                    ]
                )
            ]
                    ), 
    ]
); 


## COMENZAMOS A DIBUJAR LA TABLA

$cellDefinitions    =   [
    new JSTableCell(
        PropertyName: 'Fecha_Asignacion',
        Label: 'Fecha Asignacion',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Nombre_Producto',
        Label: 'Nombre Producto',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Cantidad',
        Label: 'Cantidad',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Precio_Producto',
        Label: 'Precio Producto',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Marca',
        Label: 'Marca',
        Colspan: 2,
        // PropertyList :[
        //     "marca"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'idMarca',
        //         JSFilterName: 'NombreMarca'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'NombreMarca',
        //         Script: [
        //             '
        //             var Marca = element.marca.Descripcion;

        //             return `<center> <span class="Center">${Marca}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Modelo',
        Label: 'Modelo',
        Colspan: 2,
        // PropertyList :[
        //     "modelo"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'idModelo',
        //         JSFilterName: 'NombreModelo'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'NombreModelo',
        //         Script: [
        //             '
        //             var Modelo = element.modelo.Descripcion;

        //             return `<center> <span class="Center">${Modelo}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Empresa',
        Colspan: 2,
        Label: 'Empresa producto',
        // PropertyList :[
        //     "empresa"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'IdEmpresa',
        //         JSFilterName: 'NombreEmpresa'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'NombreEmpresa',
        //         Script: [
        //             '
        //             var Empresa = element.empresa.Descripcion;

        //             return `<center> <span class="Center">${Empresa}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Empresa_asignado',
        Label: 'Empresa asignado',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Departamento',
        Label: 'Departamento',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Ubicacion',
        Label: 'Ubicacion',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Tipo_Tonner',
        Label: 'Tipo tonner',
        Colspan: 2,
        BodyClasses :   ["center"],
        WidgetFunction: function($x) {
            return new JSTableContent(
                PropertyName: 'Tipo_Tonner',
                JSFilterName: 'EstadoTipo'
            );
        },
        JSDataFilter: [
            new JSTableScriptFilter(
                FunctionName:'EstadoTipo',
                Script: [
                    '
                    var newtipo = element.Tipo_Tonner;

                    if ( newtipo == "Alternativo"){
                         return `<center> <span class="badge badge-soft-warning">Alternativo</span> </center>`;
                    }else{
                         return`<center> <span class="badge badge-soft-success">Original</span> </center>`;
                    }
                    '
                ]
            )
        ],    
    ),
] ;

  
//AGREGAMOS LA INFORMACION QUE TENDRA LA TABLA 
$display->AddTableFromCollection(
    tableKey: 'tbListadoStock',
    RowIdFieldName: 'id_stock',
    RowAttributeNames: ['id_stock'],
    CellDefinitions: $cellDefinitions,
    Data: $data['Stock'],
    Buttons: [
        new JSTableButton(
            Key             :   'btnEditarEntregado',
            Child           :   new FaIcon('fa-edit'),
            Classes         :   ['btn-sm'],
            OnClickClass    :   'btnEditarEntregado',
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
        FileName        :   "Lista Stock Entregado",
        Estado          :   null,
        TableKey        :   'tbListadoEntregado'
    ),
    CustomDataTable: new DataTableSettingsFilterDto(
        HideAllButtons  : false,
        CustomPdf       : false,
        TableHasButtons : true,
        GroupedButtons  : false,
    )
);


// GENERAMOS LA ESTRUCTURA QUE TIENE LA TABLA
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
                        $display->Widgets()['tbListadoStock']
                    ]
                )
            ]
        )
    ]
);

## DIBUJAMOS LA TABLA COMO TAL
$content->Draw();


// ## DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:true);


?>

@@RenderBundle(entregadoJS)

