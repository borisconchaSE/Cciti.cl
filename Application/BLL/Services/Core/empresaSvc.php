<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\empresaDao;
use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\Dao\Entities\Core\empresa;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class empresaSvc extends GenericSvc
{
    use empresaSvcT;

    function __construct($domain)
    {
        parent::__construct(
            empresaDto::class, 
            empresa::class,
            new empresaDao($domain)
        );
    }

}