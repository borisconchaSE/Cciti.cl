<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute]
class Widget {

    public function __construct(
        public string $Template = '',
        public string $Extension = '.html',
        public string $Path = 'Templates',
        public array  $Templates = [],
    ) {}

}