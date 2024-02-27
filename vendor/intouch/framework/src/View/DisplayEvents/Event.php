<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;

abstract class Event {

    abstract function GetStandardCall(?object $element = null);

    public function GetScript(?object $element = null) {

        // Recorrer acciones
        //
        $scriptContent = "";
        foreach($this->Actions as $action) {

            if ($scriptContent != "") {
                $scriptContent .= "\n\n";
            }

            $scriptContent .= $action->GetActionScript($element);
        }

        return $this->BuildScript($element, $scriptContent);
    }

    public function __construct(
        public string $jsEvent,
        public array $Actions = []
    )
    {
    }

    private function BuildScript(?object $element = null, string $scriptContent) {

        // Agregar llamado estandar de funcion de evento
        //
        $callScript = $this->GetStandardCall($element);

        if (!isset($callScript)) {
            $callScript = "";
        }

        // Aquí se debe construir el código javascript
        //
        $script = "
    $('#" . $element->Key . "').on('" . strtolower($this->jsEvent) . "', function(event) {
            " . $callScript . "
            " . $scriptContent . "
    });
";

        return $script;
    }

}