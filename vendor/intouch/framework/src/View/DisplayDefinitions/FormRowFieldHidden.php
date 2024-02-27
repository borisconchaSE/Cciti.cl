<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldHidden extends FormRowField {

    public function __construct(
        string $PropertyName = '',
        int    $Colspan = 1,
    )
    {
        parent::__construct(
            FieldType:      FormRowFieldTypeEnum::INPUT_HIDDEN,
            PropertyName:   $PropertyName,       
            Required:       false,
            Disabled:       false,
            Value:          ''
        );
    }

}