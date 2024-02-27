<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Data;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'DataCollection', Path: '../Templates', Extension: '.js')]
class DataCollection extends GenericWidget {

    public function __construct(
        public ?array   $DataItems  = null,
        string          $Category   = '',
    ) {        
        $data = "";
        if (isset($DataItems)) {
            foreach($DataItems as $item) {
                if ($data != "") {
                    $data .= ",\n";
                }
                $data .= (new DataItem($item, $Category))->Draw(false);
            }
        }   
        
        parent::__construct(
            Replace: [
                'DATA'      => $data,
            ]
        );

    }

}