<?php

namespace Intouch\Framework\View\DisplayEventActions;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class CallFunctionAction extends Action {

    public function __construct(
        public string   $FunctionName,
        public int      $Timeout = 0
    ) {
    }

    public function GetScriptContent(?FormRowField $field, ?object $object, array $fields = [], array $formGroups = [], array $rows = [], string $formKey) : string {
        
        $function = $this->FunctionName . "({
            Element         : me, 
            FormKey         :'" . $formKey . "',
            FormData        : myForm,
            FormElements    : myElements
        })";

        if ($this->Timeout > 0) {
            return "
        setTimeout( function() {" . $function . "}, " . $this->Timeout . ");
            ";
        }
        else {
            return "
        " . $function . ";
            ";
        }

    }
}