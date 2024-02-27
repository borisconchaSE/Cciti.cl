<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\UsuarioDao;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\Dao\Entities\Core\Usuario;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class UsuarioSvc extends GenericSvc
{
    use UsuarioSvcT;

    function __construct($domain)
    {
        parent::__construct(
            UsuarioDto::class, 
            Usuario::class,
            new UsuarioDao($domain)
        );
    }

}