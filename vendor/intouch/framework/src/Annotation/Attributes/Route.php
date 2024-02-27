<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute]
class Route {

    public string $MethodName = '';
    public string $Classname = '';
    public array  $MethodParameters = [];
    public string $MethodReturnType = '';

    public function __construct(
        public string $Path = '',
        public string $FullPath = '',
        public array  $Methods = [],
        public string $Authorization = '',
        public string $AppKey = '',
        public array  $Roles = [],
        public bool   $RequireSession = true
    ) {}

}