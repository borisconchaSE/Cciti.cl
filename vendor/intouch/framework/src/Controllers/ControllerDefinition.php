<?php

namespace Intouch\Framework\Controllers;

use Intouch\Framework\Annotation\Attributes\Route;

class ControllerDefinition {

    public function __construct(
        public string $ControllerClass = '',
        public string $ControllerClassname = '',
        public string $ControllerNamespace = '',
        public string $ControllerFilename = '',
        public ?Route $ControllerRoute = null,
        public ?array $ControllerMethodRoutes = null,
    ) {}

}