<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'SexyTabPage')]
class SexyTabPage extends GenericWidget {

    public function __construct(
        public string       $Key,
        public int          $Order,
        public string       $Content = '',
        public array        $TopButtons = [],
        public array        $Classes = [],
        public array        $Styles = [],
        public array        $Attributes = [],
        public array        $Properties = [],
        public array        $ContentClasses = [],
        public array        $ContentStyles = [],
        public array        $ContentAttributes = [],
        public array        $ContentProperties = [],
    )
    {
        $number = '00' . $Order;
        $number = substr($number, -3);

        $key = $number . '-' . $Key;

        // CLASSES
        $this->AddClasses($Classes);
        $this->AddClasses(['ui-tabs-panel ui-widget-content ui-corner-bottom']);

        if ($Order == 1) {
            $this->AddClasses(['ui-tabs-active ui-state-active']);
        }

        $this->AddClasses($ContentClasses, 'content');    

        // STYLES
        $this->AddStyles($Styles);
        $this->AddStyles($ContentStyles, 'content');

        // ATTRIBUTES
        $this->AddAttributes($Attributes);
        $this->AddAttributes([
            'id'                => $key,
            'aria-labelledby'   => 'ui-id-' . $Order + 3,
            'role'              => 'tabpanel',
            'aria-hidden'       => (($Order > 1) ? 'true' : 'false'),
        ]);

        if ($Order > 1) {
            $this->AddStyle('display', 'none');
        }

        // PROPERTIES
        $this->AddProperties($Properties);
        $this->AddProperties($ContentProperties, 'content');

        $topButtons  = [];
        foreach($TopButtons as $button) {
            if ($button instanceof ActionButton) {
                array_push($topButtons, $button);                
            }
        }

        $botonera = new Container(
            Classes: ['refresh-button-container'],
            Children: [
                new Container(
                    Classes: ['refresh-button-div'],
                    Styles: [
                        'z-index' => '999',
                    ],
                    Children: $topButtons
                )
            ]
        );

        parent::__construct(Replace: [
            'TOPBUTTONS'        => $botonera->Draw(false),
            'CONTENT'           => $Content,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'CONTENTCLASSES'     => $this->DrawClasses('content'),
            'CONTENTSTYLES'      => $this->DrawStyles('content'),
            'CONTENTATTRIBUTES'  => $this->DrawAttributes('content'),
            'CONTENTPROPERTIES'  => $this->DrawProperties('content'),
        ]);
    }
}