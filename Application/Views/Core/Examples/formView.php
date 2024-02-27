<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Panel;

// PageHeader
// (Utilizar el snippet: wgPageHeader)
(new PageHeader(
    Title: "Página de Ejemplo",
    Description: "Dibujar un Formulario"
))->Draw();

?>

@@Layout(authenticated)

<?php

// Para agregar un formulario administrador, utilizamos la clase "Display"
$display = new Display();

// snippet: wgForm
$display->AddFormFromObject(
    formKey: 'frmPersona',
    object: (object)[
        'IdPersona'		=> 0,
        'Nombre'        => '',
        'IdRegion'      => 0,
        'IdComuna'      => 0
    ],  
    fillData: false,
    keyFieldName: 'IdPersona',    
    rowGroups: [
        // snippet: wgFormRowGroup
        new FormRowGroup(
            Key: 'grp-identificacion-persona',
            Title: 'Identificación Persona',
            Rows: [
                // Cada fila que desee agregar, será un arreglo  []
                // Dentro de ese arreglo, utilice los snippets wgFormFieldxxxx para generar campos
                [
                    // snippet: wgFormFieldText                    
                    new FormRowFieldText(
                        PropertyName: 'Nombre',
                        FieldType: FormRowFieldTypeEnum::INPUT_TEXT,
                        Label: 'Nombre Completo',
                        Colspan: 3,
                        Required: true,
                        Events: [
                        ]
                    ),
                    // snippet: wgFormFieldSelect
                    new FormRowFieldSelect(
                        PropertyName        : 'IdRegion',
                        Label               : 'Región',
                        Required            : true,
                        Colspan             : 3,
                        SelectDefinition    : new FormRowFieldSelectDefinition(
                            Key                 : 'IdRegion',
                            Description         : 'Descripcion',
                            Values              : $data->Regiones, 
                            MultipleSelection   : false
                        )
                    ),
                ],
            ],
        ),
    ]
);



(
    new Container(
        Classes: ['view-content'],
        Styles: [],
        Children: [
            new Panel(
                Body: new Container(
                    Classes: [],
                    Styles: [],
                    Children: [
                        // Aca agregamos el formulario para que el panel lo dibuje                        
                        $display->Widgets()['frmPersona'],
                    ]
                ),
            )
        ]
    )
)->Draw();

// Cuando se utilizan objetos de formulario, fieldselect, etc, se debe
// indicar la salida de los scripts automáticos del sistema
$display->DrawScripts();