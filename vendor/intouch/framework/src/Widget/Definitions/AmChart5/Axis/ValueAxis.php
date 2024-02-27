<?php

namespace Intouch\Framework\Widget\Definitions\AmChart5\Axis;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'ValueAxis', Path: '../Templates', Extension: '.js')]
class ValueAxis extends Axis {

    public function __construct(
                string  $Key,
                ?bool   $RightSide = null,
        public  ?int    $Min = null,
        public  ?int    $Max = null,
    ) {

        $options = '';

        if (isset($RightSide) && $RightSide) {
            $options = 'opposite: true';
        }

        parent::__construct(
            Key     : $Key,
            Replace: [
                'KEY'           => $Key,
                'RENDEROPTIONS' => $options,
                'ROOTKEY'       => function() { return $this->RootKey; }, // debe ser funcion, para que evalúe el valor al momento de dibujarse y no en el constructor
                'CHARTKEY'      => function() { return $this->ChartKey; },
            ]
        );
    }
}