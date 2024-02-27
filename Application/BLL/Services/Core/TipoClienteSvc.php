<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoClienteDao;
use Application\BLL\DataTransferObjects\Core\TipoClienteDto;
use Application\Dao\Entities\Core\TipoCliente;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoClienteSvc extends GenericSvc
{
    use TipoClienteSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoClienteDto::class, 
            TipoCliente::class,
            new TipoClienteDao($domain)
        );
    }

}