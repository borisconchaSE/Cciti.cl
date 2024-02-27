<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\marcaDao;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\Dao\Entities\Core\marca;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class marcaSvc extends GenericSvc
{
    use marcaSvcT;

    function __construct($domain)
    {
        parent::__construct(
            marcaDto::class, 
            marca::class,
            new marcaDao($domain)
        );
    }

}