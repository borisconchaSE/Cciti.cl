<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldCheck;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldContent;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\Event;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\PopUpModal\ColorLineStyleEnum;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\PopUpContent;

$display = new Display();

$formRowGroup = [
    new FormRowGroup(
        Key: 'optionOne',
        Rows: [
            [
                new FormRowFieldContent(
                    GroupClass:'text-center',
                    Colspan: 4,
                    Content : new Container(
                        Children:[
                            new Html('
                                <input class="form-check-input pdf-method-export" value="1" type="radio" name="pdfExportOption" id="pdfExportOption1" checked>
                                <label class="form-check-label" for="pdfExportOption1">
                                  Un Codigo por pagina
                                </label>
                                <label class="form-check-label" for="pdfExportOption1">
                                    <img  src="'. SITE_URL .'/images/qrExamples/uno_por_pagina.jpg" style="border: black solid 1px; cursor:pointer;" alt="one por pagina" height="50%;" width="50%"/>
                                </label>
                            ')
                        ]
                    )
                ),
                new FormRowFieldContent(
                    GroupClass:'text-center',
                    Colspan: 4,
                    Content : new Container(
                        Children:[
                            new Html('
                                <input class="form-check-input pdf-method-export" value="2" type="radio" name="pdfExportOption" id="pdfExportOption2">
                                <label class="form-check-label" for="pdfExportOption2">
                                  Seis codigos por pagina
                                </label>
                                <label class="form-check-label" for="pdfExportOption2">
                                    <img src="'. SITE_URL .'/images/qrExamples/seis_por_pagina.jpg" style="border: black solid 1px; cursor:pointer;" alt="seis por pagina" height="50%" width="50%"/>
                                </label>                                
                            ')
                        ]
                    )
                ),
              
                new FormRowFieldContent(
                    GroupClass:'text-center',
                    Colspan: 4,
                    Content : new Container(
                        Children:[ 
                            new Html('
                                <input class="form-check-input pdf-method-export" value="3" type="radio"  name="pdfExportOption" id="pdfExportOption3">
                                <label class="form-check-label" for="pdfExportOption3">
                                  Doce codigos por pagina
                                </label>
                                <label class="form-check-label" for="pdfExportOption3">
                                    <img src="'. SITE_URL .'/images/qrExamples/doce_por_pagina.jpg" style="border: black solid 1px; cursor:pointer;" alt="doce por pagina" height="50%" width="50%"/>
                                </label>                                
                            ')
                        ]
                    )
                ),
                 
            ]
        ]
    )
] ;


$display->AddFormFromObject(
    formKey     : 'pdfOption',
    object      : (object) [],
    rowGroups   : $formRowGroup,
    keyFieldName: 'pdfOption',
) ;

$display->AddButton(
    new FormButton(
        Key         : 'btnGenerarPdf',
        FormKey     : 'pdfOption',
        ButtonStyle : ButtonStyleEnum::BUTTON_DANGER,
        Child       : new FaIconText('fa-file-pdf-o','Exportar'),
        Events      : [
            new FormButtonOnClickEvent()
        ]
    )
) ;


$popup = new PopUpContent(
    Key: 'PopUpPDfSettings',
    ColorLineStyle: ColorLineStyleEnum::LINE_SUCCESS,
    Title: 'Opciones de Exportación',
    DismissButtonText: 'Cancelar',
    SubTitle: 'Ingrese bajo que formato de PDF desea exportar',
    Content: new Container(
        Children: [
            $display->Widgets()['pdfOption']
        ]
    ),
    Buttons:[
        $display->Widgets()['btnGenerarPdf']
    ]
    
);

$popup->Draw();


$display->DrawScripts(addLoadEvent:false);

