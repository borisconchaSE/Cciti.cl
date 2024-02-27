<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\centrocostosDao;
use Application\BLL\DataTransferObjects\Core\centrocostosDto;
use Application\Dao\Entities\Core\centrocostos;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class centrocostosSvc extends GenericSvc
{
    use centrocostosSvcT;

    function __construct($domain)
    {
        parent::__construct(
            centrocostosDto::class, 
            centrocostos::class,
            new centrocostosDao($domain)
        );
    }

}