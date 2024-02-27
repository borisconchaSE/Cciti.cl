<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\PopUpContent;
use Intouch\Framework\Widget\Text;

$display = new Display();

$popup = new PopUpContent(
    Key     : 'popupContent',
    Title   : 'Visualizar Codigo QR',
    Content : new Container( 
        Children:[            
            new Container(
                Classes:['row'],
                Children:[
                    new Container(
                        Classes:['col-md-4'],
                        Children:[
                            new Text(
                                Classes:['title-content-text-blue'],
                                Styles:[ 
                                    ['font-weight', '500'],
                                    ['font-variant-caps', 'small-caps'],
                                    ['font-size', '16px'],
                                    ['color',' #3498db'] 
                                ],
                                Content: 'Tipo de Destino'
                            ),
                            new Html('<br>'),
                            new Text(
                                Content: $data->Destino->TipoDestino
                            )
                        ]
                    ),
                    new Container(
                        Classes:['col-md-4'],
                        Children:[
                            new Text(
                                Classes:['title-content-text-blue'],
                                Styles:[ 
                                    ['font-weight', '500'],
                                    ['font-variant-caps', 'small-caps'],
                                    ['font-size', '16px'],
                                    ['color',' #3498db'] 
                                ],
                                Content: 'Nombre Destino'
                            ),
                            new Html('<br>'),
                            new Text(
                                Content: $data->Destino->Descripcion
                            )
                        ]
                    ),
                    new Container(
                        Classes:['col-md-4'],
                        Children:[
                            new Text(
                                Classes:['title-content-text-blue'],
                                Styles:[ 
                                    ['font-weight', '500'],
                                    [ 'font-variant-caps', 'small-caps'],
                                    ['font-size', '16px'],
                                    ['color',' #3498db'] 
                                ],
                                Content: 'Enlace'
                            ),
                            new Html('<br>'),
                            new Text(
                                Content: $data->Destino->Enlace
                            )
                        ]
                    ),
                ]
            ) ,
            new Html('<hr>'),
            new Container(
                Classes:['text-center'],
                Children:[
                    new Html(
                        '<img src="'. $data->base64 .'"  style="width :auto; height:auto;max-height: 400px; max-width: 400px;">'
                    )
                ]
            )
        ]
    )
);

$popup->Draw(); 
$display->DrawScripts(addLoadEvent:false);