<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\ordencompraDao;
use Application\BLL\DataTransferObjects\Core\ordencompraDto;
use Application\Dao\Entities\Core\ordencompra;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ordencompraSvc extends GenericSvc
{
    use ordencompraSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ordencompraDto::class, 
            ordencompra::class,
            new ordencompraDao($domain)
        );
    }

}