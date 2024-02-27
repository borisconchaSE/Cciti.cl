<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;

#[Widget(Template: 'Nav')]
class Nav extends GenericWidget {

    public function __construct(        
        public array     $Children,
        public string    $Rol = '',
        public string    $Key = '',
        public array     $Classes = [],
        public array     $Styles = [],
        public array     $Attributes = [],
        public array     $Properties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        if ($Rol != '') {
            $this->AddAttribute('role', $this->Rol);
        }

        // Properties
        $this->AddProperties($Properties);

        $builder = "";
        foreach ($Children as $child) {
            if ($child instanceof GenericWidget) {
                if ($builder != "") {
                    $builder = $builder . "\n";
                }
                $builder .= $child->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'CONTENT'       => $builder,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}