<?php

namespace Intouch\Framework\Widget\Definitions\AmChart5\Axis;

abstract class XAxis extends Axis {

    public function __construct(
        string          $Key,
        array           $Replace,
    ) {        

        parent::__construct(
            Key:        $Key,
            Replace :   $Replace,
        );
    }

}