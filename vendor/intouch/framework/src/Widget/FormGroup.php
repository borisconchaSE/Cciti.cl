<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroup')]
class FormGroup extends GenericWidget {
    

    public function __construct(
        public array  $Rows,
        public string $Key = '',
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = []
    )
    {        
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('form-group');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);
        
        // Agregamos las filas
        $first = true;
        $formGroupRows  = '';

        if (isset($Rows)) {
            foreach($Rows as $row) {
                if ($row instanceof FormGroupRow) {
                    $row->First = $first;
                    $first = false;

                    $formGroupRows .= $row->Draw(false);
                }
                else if ($row instanceof FormGroupSeparator) {
                    $formGroupRows .= $row->Draw(false);
                }
            }
        }

        parent::__construct(Replace: [
            'ROWS'              => $formGroupRows,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties()
        ]);
    }
}