<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'Html')]
class Html extends GenericWidget {

    public function __construct(
        public string $Content = ''
    )
    {
        parent::__construct(Replace: [
            'CONTENT'   => $Content
        ]);
    }
}