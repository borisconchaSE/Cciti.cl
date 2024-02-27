<?php

namespace Intouch\Framework\Widget\Definitions\AmChart5\Axis;

use Intouch\Framework\Widget\Definitions\AmChart5\Basic;

abstract class Axis extends Basic {

    public string $ChartKey = '';

    public function __construct(
        string  $Key,
        array   $Replace,
    ) {

        parent::__construct(
            Key     : $Key,
            Replace: $Replace
        );
        
    }

}