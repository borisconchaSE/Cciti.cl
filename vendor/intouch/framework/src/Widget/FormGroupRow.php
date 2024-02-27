<?php

namespace Intouch\Framework\Widget;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroupRow')]
class FormGroupRow extends GenericWidget {

    public function __construct(
        public ?array $Columns,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public bool   $First = false,
        public ?int    $Layout = TipoLayoutFilaEnum::BOOTSTRAP
    )
    {        
        // Classes
        $this->AddClasses($Classes);

        if ($Layout == TipoLayoutFilaEnum::BOOTSTRAP) {
            $this->AddClass('row');
        }
        else if ($Layout == TipoLayoutFilaEnum::FLEXBOX) {
            $this->AddStyle('display', 'flex');
        }

        if (!$First) {
            $this->AddClass('inputline');
        }
        
        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);
        
        // Agregamos las columnas
        $formGroupColumns  = '';

        if (isset($Columns)) {
            foreach($Columns as $col) {
                if ($col instanceof GenericWidget || $col instanceof FormGroupColumn || $col instanceof FormGroupColumnEmpty || $col instanceof FormGroupColumnLabel) {
                     $formGroupColumns .= $col->Draw(false);
                }
            }
        }

        parent::__construct(Replace: [
            'COLUMNS'       => $formGroupColumns,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}