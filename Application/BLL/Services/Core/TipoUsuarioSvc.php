<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoUsuarioDao;
use Application\BLL\DataTransferObjects\Core\TipoUsuarioDto;
use Application\Dao\Entities\Core\TipoUsuario;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoUsuarioSvc extends GenericSvc
{
    use TipoUsuarioSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoUsuarioDto::class, 
            TipoUsuario::class,
            new TipoUsuarioDao($domain)
        );
    }

}