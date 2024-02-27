<?php

namespace Intouch\Framework\MailHelper;

class Recipient {

    public function __construct(
        public string $Address,
        public string $Name = '',
        public int    $Type = RecipientTypeEnum::TO_RECIPIENT
    ) {}

}