<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'SimpleList')]
class SimpleList extends GenericWidget {

    public function __construct(
        public string  $Key = '',
        public array   $Items = [],
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = [],
        public array   $ItemClasses = [],
        public array   $ItemStyles = [],
        public array   $ItemAttributes = [],
        public array   $ItemProperties = [] 
    )
    {        
        // Classes
        $this->AddClasses($this->Classes);

        // Styles
        $this->AddStyles($this->Styles);
        $this->AddStyle('padding-left', '18px');

        // Attributes
        $this->AddAttributes($this->Attributes);

        // Properties
        $this->AddProperties($this->Properties);

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        $items = '';
        foreach($this->Items as $item) {

            if (is_string($item)) {

                if ($items != '') {
                    $items .= "\n";
                }

                $items .= (
                    new SimpleListItem(
                        Content:    $item,
                        Classes:    $this->ItemClasses,
                        Styles:     $this->ItemStyles,
                        Attributes: $this->ItemAttributes,
                        Properties: $this->ItemProperties
                    )
                )->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'ITEMS'         => isset($items) ? $items : '',
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}