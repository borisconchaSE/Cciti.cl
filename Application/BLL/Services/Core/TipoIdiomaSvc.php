<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoIdiomaDao;
use Application\BLL\DataTransferObjects\Core\TipoIdiomaDto;
use Application\Dao\Entities\Core\TipoIdioma;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoIdiomaSvc extends GenericSvc
{
    use TipoIdiomaSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoIdiomaDto::class, 
            TipoIdioma::class,
            new TipoIdiomaDao($domain)
        );
    }

}