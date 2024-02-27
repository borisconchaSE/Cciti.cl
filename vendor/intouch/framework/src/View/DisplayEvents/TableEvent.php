<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;

abstract class TableEvent {

    abstract function GetStandardCall(TableCell | TableButton | JSTableButton  $element, string $tableKey);

    public function GetScript(TableCell | TableButton | JSTableButton $element, ?object $object, string $tableKey) {

        // Recorrer acciones
        //
        $scriptContent = "";
        foreach($this->Actions as $action) {

            if ($scriptContent != "") {
                $scriptContent .= "\n\n";
            }

            $scriptContent .= $action->GetActionScript($element, $object, $tableKey);
        }

        return $this->BuildScript($element, $scriptContent, $tableKey);        
    }

    public function __construct(
        public string $jsEvent,
        public array $Actions = []
    )
    {
    }

    private function BuildScript(TableCell | TableButton | JSTableButton $element, string $scriptContent, string $tableKey) {

        // Agregar llamado estandar de funcion de evento
        //
        $callScript = $this->GetStandardCall($element, $tableKey);

        if ($element instanceof TableCell) {
            $jKey = '#' . $element->PropertyName;
            $key = $element->PropertyName;
        }
        else if ($element instanceof TableButton || $element instanceof JSTableButton ) {

            $key = $element->Key;
            
            if ($element->OnClickClass != '') {
                $jKey = '.' . $element->OnClickClass . '.new';
            }
            else {
                $jKey = '#' . $element->Key;
            }
        }

        if (!isset($callScript)) {
            $callScript = "
            // Llamar evento estandar
            if (typeof " . str_replace('-', '_', $key) . "_On" . $this->jsEvent . " === 'function') {
                " . str_replace('-', '_', $key) . "_On" . $this->jsEvent . " (myRowData);
            }
                ";
        }

        // Aquí se debe construir el código javascript
        //
        $script = "
        $('" . $jKey . "').on('" . strtolower($this->jsEvent) . "', function(event) {
            
            var me          = $(this)[0];
            var row         = $(this).closest('tr');        

            if (typeof ReadData === 'function') {
                myRowData = ReadData($(row));
            }

                " . $callScript . "
                " . $scriptContent . "
        });
    ";

        return $script;
    }

}