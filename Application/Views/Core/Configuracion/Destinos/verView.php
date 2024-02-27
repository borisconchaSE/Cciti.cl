<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldContent;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\FormOnChangeEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\ScriptDirect;
use Intouch\Framework\Widget\Text;

?>

@@Layout(authenticatedperiodo)

<?php

(new PageHeader(
    Title: 'Ver/Editar Destinos',
    Description: 'Configuración de destinos y enlaces para la generación de códigos QR',
    IconName: 'fa-qrcode', 
))->Draw();

$display = new Display();


$display->AddButton(
    new FormButton(
        Key: 'BtnBuscar',
        FormKey: 'frmencabezado',
        ButtonStyle: ButtonStyleEnum::BUTTON_INFO,
        Classes: ['pull-right'],
        Child: new Text('Buscar'),
        Events: [ 
            new FormButtonOnClickEvent() ,  
        ]
    )
);

// Crear el formulario
$display->AddFormFromObject(
    formKey: 'frmencabezado',
    object: $data->Destino,
    fillData: false,
    keyFieldName: 'IdDestino',
    rowGroups: [
        new FormRowGroup(
            Key: 'grp-informacion-destino',
            Rows: [
                [
                    new FormRowFieldSelect(
                        PropertyName: 'IdTipoDestino',
                        Label: 'Tipo de Destino',
                        Colspan: 3,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values: $data->TiposDestino,
                            Key: 'IdTipoDestino',
                            Description: 'Descripcion',
                            TriggerChangeOnload: true,
                        ),
                        Events: [
                            new FormOnChangeEvent()
                        ]
                    ),
                    new FormRowFieldSelect(
                        PropertyName: 'IdTipoEstadoDestino',
                        Label: 'Estado',
                        Colspan: 3,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values: $data->Estados,
                            Key: 'IdTipoEstadoDestino',
                            Description: 'Descripcion',
                        ),
                        Events: [
                            new FormOnChangeEvent()
                        ]
                    ),
                    new FormRowFieldText(
                        PropertyName: 'Descripcion',
                        Label: 'Nombre Destino',
                        Colspan: 3
                    ),
                    new FormRowFieldContent(
                        Colspan:1,
                        Content: new Container(
                            Padding: new Edge(Left:0, Top:25, Right:0, Bottom: 0),
                            Classes: ['text-left'],
                            Children: [
                                $display->Widgets()['BtnBuscar']
                            ]
                        )
                    )
                ],
            ]
        ),
    ]
);


(new Container(
    Classes: ['view-content'],
    Children: [
        new Panel(
            Header: new Container(
                Children: [
                    $display->Widgets()['frmencabezado']
                ]                        
            ),
            Body: new Container(
                Key: 'formulario-destinos-contenido',
                Children: []
            ),
        )
    ]
))->Draw();
?>

@@IncludeScriptBundle(DestinosJS)

<?php

$display->DrawScripts();

$script = new ScriptDirect([
    "

    setTimeout(() => {
    
        if( $('#BtnBuscar') ) {
            $('#BtnBuscar').click();
        }
    }, 200);
       
    "
]);
$script->Draw();
