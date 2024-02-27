<?php

namespace Intouch\Framework\MailHelper;

class ComposeReplacement {

    public function __construct(
        public Recipient    $Recipient,    // el recipiente objetivo del reemplazo de textos
        public array        $Tokens,       // los textos a reemplazar
        public array        $Attachments = []   // los archivos a adjuntar
    ) {
    }
}