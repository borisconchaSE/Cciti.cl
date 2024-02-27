<?php
namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'ChartContainer')]
class ChartGeneric extends GenericWidget{

    public function __construct(
        public string  $Key,
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = [],

        
    )
    {
        //DEFINICION DEL GENERIC WIDGET
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            //'SCRIPT'        => $script->Draw(false),
        ]);
    }

    private function ScriptRootElement() {

        return "var root_" . $this->Key . " = am5.Root.new('" . $this->Key . "')";

    }


}
