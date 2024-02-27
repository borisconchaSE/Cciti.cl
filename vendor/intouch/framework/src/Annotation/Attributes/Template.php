<?php

namespace Intouch\Framework\Annotation\Attributes;

class Template {

    public function __construct(
        public string $Template = '',
        public string $Extension = '.html',
        public string $Path = 'Templates'
    ) {}

}