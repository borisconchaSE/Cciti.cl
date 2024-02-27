<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'TitleDescription')]
class TitleDescription extends GenericWidget {

    public function __construct(
        public string   $Title,
        public string   $Description = '&nbsp;',
        public ?string  $IconName = null,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = [],
        public array    $TitleClasses = [],
        public array    $TitleStyles = [],
        public array    $TitleAttributes = [],
        public array    $TitleProperties = [],
        public array    $DescriptionClasses = [],
        public array    $DescriptionStyles = [],
        public array    $DescriptionAttributes = [],
        public array    $DescriptionProperties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses($TitleClasses, 'title');
        $this->AddClasses(['font-light title-text'], 'title');
        $this->AddClasses($DescriptionClasses, 'description');
        $this->AddClasses(['font-light description-text'], 'description');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles($TitleStyles, 'title');
        $this->AddStyles($DescriptionStyles, 'description');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($TitleAttributes, 'title');
        $this->AddAttributes($DescriptionAttributes, 'description');

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($TitleProperties, 'title');
        $this->AddProperties($DescriptionProperties, 'description');

        if (isset($IconName)) {
            $icon = (
                new FaIcon(
                    Name: $IconName,
                    Styles: [
                        ['font-size', '36px'], ['color', '#ededed']
                    ]
                )
            )->Draw(false);
            $this->AddStyle('display', 'flex', 'main');
            $this->AddStyle('flex-grow', '1');
            $this->AddStyle('width', '52px', 'icon');
        }
        else {
            $icon = '';
        }

        parent::__construct(Replace: [
            'TITLE'                 => $this->Title,
            'DESCRIPTION'           => $this->Description,
            'ICON'                  => $icon,
            'MAINSTYLES'            => $this->DrawStyles('main'),
            'ICONSTYLES'            => $this->DrawStyles('icon'),
            'CLASSES'               => $this->DrawClasses(),
            'STYLES'                => $this->DrawStyles(),
            'ATTRIBUTES'            => $this->DrawAttributes(),
            'PROPERTIES'            => $this->DrawProperties(),
            'TITLECLASSES'          => $this->DrawClasses('title'),
            'TITLESTYLES'           => $this->DrawStyles('title'),
            'TITLEATTRIBUTES'       => $this->DrawAttributes('title'),
            'TITLEPROPERTIES'       => $this->DrawProperties('title'),
            'DESCRIPTIONCLASSES'    => $this->DrawClasses('description'),
            'DESCRIPTIONSTYLES'     => $this->DrawStyles('description'),
            'DESCRIPTIONATTRIBUTES' => $this->DrawAttributes('description'),
            'DESCRIPTIONPROPERTIES' => $this->DrawProperties('description')
        ]);
    }
}