<?php

namespace Intouch\Framework\View\DisplayEventActions;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class ChangeVisibilityAction extends Action {

    public function __construct(
        public string $TargetElementKey,
        public int    $ActionType = VisibilityActionEnum::SHOW_ONCHECKED
    ) {
    }

    public function GetScriptContent(?FormRowField $field, ?object $object, array $fields = [], array $formGroups = [], array $rows = [], string $formKey) : string {

        $script = "";        
        $value = false;

        // Buscar el valor actual del elemento
        $script .= "
        var isChecked = me.checked;
        ";

        // Buscar el elemento destino
        //
        $targetId = "";
        if (isset($fields) && isset($fields[$this->TargetElementKey])) {
            $target = $fields[$this->TargetElementKey];
            $targetId = $target->Id;
            // return "
            //     " . $this->GetJquerySelector($target->Id) . "." . ( $mostrar ? 'remove' : 'add' ) . "Class('hide');\n
            // \n";
        }
        else if (isset($formGroups) && isset($formGroups[$this->TargetElementKey])) {
            $target = $formGroups[$this->TargetElementKey];
            $targetId = $target->Key;
            // return "\n
            //     " . $this->GetJquerySelector($target->Key) . "." . ( $mostrar ? 'remove' : 'add' ) . "Class('hide');\n
            // \n";
        }

        if ($targetId != '') {

            if ($this->ActionType == VisibilityActionEnum::SHOW_ONCHECKED) {
                $script .= "

        if (isChecked) {
            " . $this->GetJquerySelector($targetId) . ".removeClass('hide');
        }
        else {
            " . $this->GetJquerySelector($targetId) . ".addClass('hide');
        }
        ";
            }
            else {
                $script .= "

        if (!isChecked) {
            " . $this->GetJquerySelector($targetId) . ".removeClass('hide');
        }
        else {
            " . $this->GetJquerySelector($targetId) . ".addClass('hide');                    
        }
        ";
            }

        }

        return $script;

    }

}