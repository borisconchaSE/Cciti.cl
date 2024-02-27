<?php

namespace Intouch\Framework\Annotation\Attributes;

use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;

#[\Attribute]
class ViewDisplay {

    public function __construct(
        public string   $FieldType = FormRowFieldTypeEnum::INPUT_TEXT,
        public string   $Label = '',
        public ?bool    $Required = null
    ) {}

}