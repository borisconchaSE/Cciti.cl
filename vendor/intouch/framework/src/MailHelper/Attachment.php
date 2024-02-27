<?php

namespace Intouch\Framework\MailHelper;

class Attachment {

    public function __construct(
        public string $FilePath,
        public string $AttachmentType = DispositionEnum::ATTACHMENT,
        public string $FileName = ''
    ) {}
    
}