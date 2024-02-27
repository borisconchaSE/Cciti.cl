<?php

use Application\BLL\DataTransferObjects\Administracion\EquipoDto;
use Application\BLL\DataTransferObjects\Administracion\VWEquiposDto;
use Application\BLL\DataTransferObjects\Etiquetado\VWDestinosDto;
use Intouch\Framework\View\Display;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\Text;

$display = new Display();

$destino = ($data?->Destino instanceof VWDestinosDto) ? $data?->Equipo : null;

$content = new Panel(
    Header  :new Text('Nuevo Código QR Generado'),
    Body    : new Container(
        Classes : ['container'],
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
                                    [ 'font-variant-caps', 'small-caps'],
                                    ['font-size', '16px'],
                                    ['color',' #3498db'] 
                                ],
                                Content: 'Tipo de Destino'
                            ),
                            new Html('<br>'),
                            new Text(
                                Content: $destino->TipoDestino
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
                                Content: 'Nombre Destino'
                            ),
                            new Html('<br>'),
                            new Text(
                                Content: $destino->Descripcion
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
                                Content: $destino->Enlace
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
) ;

$content->Draw();