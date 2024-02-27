<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoClaveDao;
use Application\BLL\DataTransferObjects\Core\TipoClaveDto;
use Application\Dao\Entities\Core\TipoClave;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoClaveSvc extends GenericSvc
{
    use TipoClaveSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoClaveDto::class, 
            TipoClave::class,
            new TipoClaveDao($domain)
        );
    }

}