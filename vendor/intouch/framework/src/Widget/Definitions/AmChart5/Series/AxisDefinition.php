<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

class AxisDefinition {

    public function __construct(
        public  string  $AxisKey,
        public  string  $PropertyName = ''
    ) {
    }

}