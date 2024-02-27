<?php

namespace Intouch\Framework\Widget\Definitions\Photo;


class PhotoDefinition {

    public function __construct(
        public string           $Key = '',
        public string           $ImageUrl,
        public \DateTime        $Date,
        public string           $DateTitle,
        public string           $AvatarUrl,
        public string           $Username = '',
        public string           $Origin = '',
        public string           $Comentario = ''
    ) {        
    }

}