<?php

namespace Intouch\Framework\Collection;

class AttachDefinition {


    public function __construct(
        public GenericCollection  $Values,
        public string $ForeignKey = "",
        public string $AttachPrimaryKey = "",
        public string $AttachPropertyName = "",
       
    )
    {
    }

}