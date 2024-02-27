<?php

namespace Intouch\Framework\Widget\Definitions\General;

use Intouch\Framework\Widget\Definitions\Container\Position;

class Badge {
    public function __construct(
        public Position $Position,
        public string $Content = '',
        public string $Key = '',
        public array $Classes = []
    ) {}
}