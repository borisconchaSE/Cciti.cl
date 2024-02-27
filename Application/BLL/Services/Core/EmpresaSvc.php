<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\EmpresaDao;
use Application\BLL\DataTransferObjects\Core\EmpresaDto;
use Application\Dao\Entities\Core\Empresa;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class EmpresaSvc extends GenericSvc
{
    use EmpresaSvcT;

    function __construct($domain)
    {
        parent::__construct(
            EmpresaDto::class, 
            Empresa::class,
            new EmpresaDao($domain)
        );
    }

}