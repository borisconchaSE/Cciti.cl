<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'SexyTab')]
class SexyTab extends GenericWidget {

    public function __construct(
        public string       $Key,
        public int          $Order,
        public string       $Title = '',
        public string       $Icon = '',
        public array        $Classes = [],
        public array        $Styles = [],
        public array        $Attributes = [],
        public array        $Properties = [],
        public array        $ActionClasses = [],
        public array        $ActionStyles = [],
        public array        $ActionAttributes = [],
        public array        $ActionProperties = [],
        public array        $TitleClasses = [],
        public array        $TitleStyles = [],
        public array        $TitleAttributes = [],
        public array        $TitleProperties = []
    )
    {
        $number = '00' . $Order;
        $number = substr($number, -3);

        $key = $number . '-' . $Key;

        // CLASSES
        $this->AddClasses($Classes);
        $this->AddClasses(['sexytab-item ui-state-default ui-corner-top']);

        if ($Order == 1) {
            $this->AddClasses(['ui-tabs-active ui-state-active']);
        }

        $this->AddClasses($ActionClasses, 'action');
        $this->AddClass('ui-tabs-anchor', 'action');

        $this->AddClasses($TitleClasses, 'title');        

        // STYLES
        $this->AddStyles($Styles);
        $this->AddStyle('cursor', 'pointer');

        $this->AddStyles($ActionStyles, 'action');
        $this->AddStyles($TitleStyles, 'title');

        // ATTRIBUTES
        $this->AddAttributes($Attributes);
        $this->AddAttributes([
            'id'        => $Key,
            'role'      => 'tab',
            'tabindex'  => '0',
            'aria-controls' => $key,
            'aria-labelledby'   => 'ui-id-' . $Order + 3,
            'aria-selected'     => ($Order == 1) ? 'true' : 'false',
            'aria-expanded'     => ($Order == 1) ? 'true' : 'false'
        ]);

        if ($Order == 1) {
            $this->AddAttribute('id', 'activas-init');
        }

        $this->AddAttributes([
            'id'        => 'ui-id-' . $Order + 3,
            'href'      => '#' . $key,
            'role'      => 'presentation',
            'tabindex'  => '-1'
        ], 'action');

        // PROPERTIES
        $this->AddProperties($Properties);
        $this->AddProperties($ActionProperties, 'action');
        $this->AddProperties($TitleProperties, 'title');


        $icon = '';
        if ($Icon != '') {
            $icon = (new FaIcon(
                Name: $Icon,
                Classes: ['fas', 'tab-icon'],
                Styles: [
                    'font-size' => '20px',
                    'position'  => 'relative',
                    'left'      => '-8px',
                    'top'       => '-5px'
                ]
            ))->Draw(false);
        }

        $title = (new Text(
            Content: $Title,
            Styles: [
                'top'   => '0px'
            ]
        ))->Draw(false);

        parent::__construct(Replace: [
            'ICON'              => $icon,
            'TITLE'             => $title,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'ACTIONCLASSES'     => $this->DrawClasses('action'),
            'ACTIONSTYLES'      => $this->DrawStyles('action'),
            'ACTIONATTRIBUTES'  => $this->DrawAttributes('action'),
            'ACTIONPROPERTIES'  => $this->DrawProperties('action'),
            'TITLECLASSES'      => $this->DrawClasses('title'),
            'TITLESTYLES'       => $this->DrawStyles('title'),
            'TITLEATTRIBUTES'   => $this->DrawAttributes('title'),
            'TITLEPROPERTIES'   => $this->DrawProperties('title')
        ]);
    }
}