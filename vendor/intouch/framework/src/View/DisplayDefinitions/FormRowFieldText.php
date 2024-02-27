<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldText extends FormRowField {

    public function __construct(
        string          $GroupKey       = '',
        string          $GroupClass     = '',
        string          $FieldType      = FormRowFieldTypeEnum::INPUT_TEXT,
        string          $PropertyName   = '',
        string          $Label          = '',
        string          $Placeholder    = '',
        int             $Colspan        = 1,        
        ?bool           $Required       = false,
        ?bool           $Disabled       = false,
        ?bool           $Multiple       = true,
        public int      $Lines          = 1,
        public bool     $Resize         = false,
        array           $Events         = [],
        ?array          $Attributes     = []

    )
    {
        parent::__construct(
            GroupKey: $GroupKey,
            GroupClass: $GroupClass,
            FieldType:      $FieldType,
            PropertyName:   $PropertyName,
            Label:          $Label,
            Placeholder:    $Placeholder,
            Colspan:        $Colspan,        
            Required:       $Required,
            Disabled:       $Disabled,
            Multiple:       $Multiple,
            Events:         $Events,
            Attributes     :   $Attributes
        );
    }

}