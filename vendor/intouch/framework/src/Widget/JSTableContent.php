<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'JSTableContent')]
class JSTableContent extends GenericWidget {

    public function __construct(
        public string  $PropertyName,
        public ?string  $JSFilterName = null,
    )
    {       

        $PropertyName = isset($PropertyName) ? "[[$PropertyName]]" : '&nbsp;';

        parent::__construct(Replace: [
            'CONTENT'       => $PropertyName,
        ]);
    }
}