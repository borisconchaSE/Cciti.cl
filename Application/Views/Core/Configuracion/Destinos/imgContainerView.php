<?php

use Application\BLL\BusinessObjects\Core\DestinosBO;
use Application\BLL\DataTransferObjects\Etiquetado\DestinoDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Image;

$display = new Display();

$imgCollection = [];

$destinosBO = new DestinosBO();

if(!empty($data) && $data instanceof GenericCollection){

    foreach($data as $item){

        if($item instanceof DestinoDto){


            $base64 = $destinosBO->BuscarQrBase64($item);

            if(!empty($base64)) {
                $imgCollection[] = new Image(
                    Source: $base64,
                    Classes:['hide']
                );
            }
        }
    }
}

$content = new Container(
    Key:'hiddenQryImage',
    Children: $imgCollection
);

$content->Draw();