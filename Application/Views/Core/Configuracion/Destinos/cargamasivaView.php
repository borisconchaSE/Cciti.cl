@@Layout(authenticatedperiodo)
<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\Widget\Action;
use Intouch\Framework\Widget\ActionButton;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\Text;

(new PageHeader(
    Title: 'Carga Masiva Destinos',
    Description: 'Carga masiva de destinos y enlaces para la generación de códigos QR',
    IconName: 'fa-qrcode', 
    AditionalContent: new Container(
        Children:[
            new Action(
                Key:'_downloadTemplate',
                Child: new ActionButton(
                    Key:'btnDescargarPlantilla',
                    ButtonStyle: ButtonStyleEnum::BUTTON_WARNING,
                    Child:new FaIconText('fa-file','Descargar Plantilla')
                ),
                Action: SITE_URL ."/images/CargaMasivaDestinos.xlsx"
            ) ,
        ]
    )
))->Draw();

$display = new Display();

$display->AddButton(
    new FormButton(
        Key: 'btnNuevaCargaMasivaExcel',
        FormKey: 'frmCargaMasivaQR',
        ButtonStyle: ButtonStyleEnum::BUTTON_SUCCESS,
     
        Child: new Text('Subir Destinos'),        
        Events: [ 
            new FormButtonOnClickEvent()
        ]
        
    )
);

 
$display->AddFormFromObject(
    formKey:'frmCargaMasivaQR',
    object : (object) [],
    keyFieldName : 'file',
    rowGroups: [
        new FormRowGroup(
            Key : 'filegroup',
            Rows:[
                    [                    
                        new FormRowFieldText(
                            FieldType: FormRowFieldTypeEnum::INPUT_FILE_EXCEL,
                            PropertyName: 'fileInput',
                            Label: 'Seleccione su archivo para la carga masiva de destinos y enlaces',
                            Colspan: 12,
                            Multiple: false
                        )
                    ],
                 
            ]
        )
    ]
) ;


(new Container(
    Classes: ['view-content'],
    Children: [
        new Panel(
             
            Body: new Container(  
                Children: [
                    $display->Widgets()['frmCargaMasivaQR']
                ]
            ),
            Footer: new Container(
                Classes:['pull-right'],
                Children:[
                    $display->Widgets()['btnNuevaCargaMasivaExcel']
                ]
            )
        ),
        new Container(
            Key: 'carga-masiva-preview',
            Children:[]
        )

    ]
))->Draw();

$display->DrawScripts();
?>

@@IncludeScriptBundle(DestinosJS)
 