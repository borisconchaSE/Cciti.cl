<?php

namespace Intouch\Framework\Widget\Definitions\AmChart5\Axis;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'CategoryAxis', Path: '../Templates', Extension: '.js')]
class CategoryAxis extends XAxis {

    public function __construct(
        string          $Key,
        public string   $PropertyName,
    ) {

        parent::__construct(
            Key     : $Key,
            Replace: [
                'KEY'           => $Key,
                'PROPERTYNAME'  => $PropertyName,
                'ROOTKEY'       => function() { return $this->RootKey; }, // debe ser funcion, para que evalúe el valor al momento de dibujarse y no en el constructor
                'CHARTKEY'      => function() { return $this->ChartKey; },
            ]
        );
    }
}