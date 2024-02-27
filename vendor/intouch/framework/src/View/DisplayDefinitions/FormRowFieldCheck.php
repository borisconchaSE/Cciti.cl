<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\View\DisplayValidations\Validation;

class FormRowFieldCheck extends FormRowField {

    public function __construct(
        string       $GroupKey = '',
        string       $GroupClass = '',
        string          $PropertyName = '',
        string          $Label = '',
        public string   $Title = '',
        string          $Placeholder = '',
        int             $Colspan = 1,        
        ?bool           $Required = false,
        ?bool           $Disabled = false,
        public ?bool    $Checked = false,
        public ?bool    $EvaluateOnLoad = false, 
        array           $Events = [],
        Validation      $ValidationRules = null,
        public array    $Attributes = []
    )
    {
        parent::__construct(
            GroupKey:       $GroupKey,
            GroupClass:     $GroupClass,
            PropertyName:   $PropertyName,
            FieldType:      FormRowFieldTypeEnum::INPUT_CHECK,
            Label:          $Label,
            Placeholder:    $Placeholder,
            Colspan:        $Colspan,        
            Required:       $Required,
            Disabled:       $Disabled,
            Events:         $Events,
            Checked:        $Checked,
        );
    }

    function GetGlobalScripts(): array {
        
        if ($this->EvaluateOnLoad) {
                return ["
    // CHECK EvaluateOnLoad=true
    $('#" . $this->Id . "').trigger('change');
                "];
        }        
        
        return [];

    }

}