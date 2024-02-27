<?php

namespace Intouch\Framework\Widget\Definitions\Container;

use Intouch\Framework\Widget\Definitions\General\UnitEnum;

class Position {

    public function __construct(
        public ?int $Left = null,
        public ?int $Right = null,
        public ?int $Top = null,
        public ?int $Bottom = null,
        public string $Unit = UnitEnum::PIXELS,
        public $Type = PositionEnum::RELATIVE
    ) {
        
    }

}