<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

#[Widget(Template: 'CardIconText')]
class CardIconText extends GenericWidget {

    public function __construct(
        public string   $Key,
        public string   $IconName,
        public string   $Text,
        public bool     $Relevant = false,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = [],
        public array    $IconClasses = [],
        public array    $IconStyles = [],
        public array    $IconAttributes = [],
        public array    $IconProperties = [],
        public array    $TextClasses = [],
        public array    $TextStyles = [],
        public array    $TextAttributes = [],
        public array    $TextProperties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('card text-center');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        array_push($IconClasses, 'card-icon');

        if ($Relevant)
            array_push($IconClasses, 'relevant');

        array_push($TextClasses, 'card-title');

        $icon = new FaIcon(
            Name: $IconName,
            Classes: $IconClasses,
            Styles: $IconStyles,
            Attributes: $IconAttributes,
            Properties: $Properties
        );

        $text = new Text(
            Content: $Text,
            Classes: $TextClasses,
            Styles: $TextStyles,
            Attributes: $TextAttributes,
            Properties: $TextProperties
        );

        // Properties
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'ICON'          => $icon->Draw(false),
            'TEXT'          => $text->Draw(false),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}