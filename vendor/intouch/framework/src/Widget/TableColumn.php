<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'TableColumn')]
class TableColumn extends GenericWidget {

    public function __construct(
        public string $Content,
        public string $PropertyName = '',
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public        $FormatFunction = null,
        public        $Object = null
    )
    {
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddProperties($Properties);

        if (isset($this->Object) && isset($this->FormatFunction) ) {
            // el objeto es un OBJECT o un ARRAY asociativo?
            if (is_array($this->Object)) {
                $obj = (object)$this->Object;
            }
            else {
                $obj = $this->Object;
            }

            // obtener el contenido evaluando la funcion
            $result = $FormatFunction($obj);
        }
        else {
            $result = $this->Content;
        }

        parent::__construct(Replace: [
            'CONTENT'       => $result,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}