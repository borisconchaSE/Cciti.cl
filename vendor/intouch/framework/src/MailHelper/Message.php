<?php

namespace Intouch\Framework\MailHelper;

class Message {

    public function __construct(
        public array  $Recipients,
        public bool   $IsHtml,
        public string $Subject,
        public string $Body,
        public string $AltBody = ''
    ) {}

}