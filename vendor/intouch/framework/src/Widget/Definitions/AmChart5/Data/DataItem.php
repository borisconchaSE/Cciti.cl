<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Data;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Mapping\Reflect;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'DataItem', Path: '../Templates', Extension: '.js')]
class DataItem extends GenericWidget {

    public function __construct(
        object  $Element,
        string  $Category = ''
    ) {

        $properties = Reflect::ToArray($Element);

        $result = '';
        foreach($properties as $item) {

            if ($result != '') {
                $result .= ",\n";
            }

            if (is_numeric($item->Value) && $item->PropertyName != $Category) {
                $value = $item->Value*1;
            }
            else {
                $value = '"' . $item->Value . '"';
            }

            $result .= "\t\t\t\t\t " . $item->PropertyName . ': ' . $value;
        }

        $data = $result;
        
        parent::__construct(
            Replace: [
                'DATA'      => $data,
            ]
        );

    }

}