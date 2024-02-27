<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'PageHeader')]
class PageHeader extends GenericWidget {

    public function __construct(
        public string           $Title,
        public string           $Description = '&nbsp;',
        public ?string          $IconName = null,
        public ?GenericWidget   $AditionalContent= null,
        public string           $Key = '',                
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses(['normalheader', 'small-header']);

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        if ($this->Key != '')
            $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        // Content
        if (isset($AditionalContent)) {
            $content = (new Container(
                Styles: [
                    ['display', 'flex']
                ],
                Children: [
                    new Container(
                        Styles: [
                            ['flex-grow', '1']
                        ],
                        Children: [
                            new TitleDescription(
                                Title: $this->Title,
                                Description: $this->Description,
                                IconName: $IconName,
                                Classes: ['text-page-title']
                            )
                        ]                    
                    ),
                    new Container(
                        Children: [$AditionalContent]
                    )
                ]
            ))->Draw(false);
        }
        else {
            $content = (new TitleDescription(
                Title: $this->Title,
                Description: $this->Description,
                IconName: $IconName,
                Classes: ['text-page-title']
            ))->Draw(false);
        }

        parent::__construct(Replace: [
            'CONTENT'       => $content,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}