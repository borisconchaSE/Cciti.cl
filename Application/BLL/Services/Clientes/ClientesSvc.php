<?php
namespace Application\BLL\Services\Clientes;

use Application\Dao\Services\Clientes\ClientesDao;
use Application\BLL\DataTransferObjects\Clientes\ClientesDto;
use Application\Dao\Entities\Clientes\Clientes;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ClientesSvc extends GenericSvc
{
    use ClientesSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ClientesDto::class, 
            Clientes::class,
            new ClientesDao($domain)
        );
    }

}