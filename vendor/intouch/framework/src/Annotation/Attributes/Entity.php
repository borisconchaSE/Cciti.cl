<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute]
class Entity {

    public function __construct(
        public string $Schema = 'dbo',        
        public String $TableName = '',
        public String $TablePrefix = ''
    ) {}

}