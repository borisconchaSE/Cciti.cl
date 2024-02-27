<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Photo\PhotoDefinition;
use Intouch\Framework\Widget\Definitions\PopUpModal\ColorLineStyleEnum;

#[Widget(Template: 'PhotoGallery')]
class PhotoGallery extends GenericWidget {

    public function __construct(
        public string           $Key,
        public array            $Items = [],
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('tz-gallery');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        $items = '';

        foreach($Items as $item) {

            if ($item instanceof PhotoDefinition) {
                $new = new Photo(
                    Key: $item->Key,
                    ImageUrl: $item->ImageUrl,
                    Date: $item->Date,
                    DateTitle: $item->DateTitle,
                    AvatarUrl: $item->AvatarUrl,
                    Username: $item->Username,
                    Origin: $item->Origin,
                    Comentario: $item->Comentario
                );

                if ($items != '') {
                    $items .= "\n";
                }

                $items .= $new->Draw(false);
            }
        }


        parent::__construct(Replace: [
            'ITEMS'         => $items,
            'KEY'           => $Key,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}