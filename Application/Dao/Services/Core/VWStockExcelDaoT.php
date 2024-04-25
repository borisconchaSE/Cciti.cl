<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWStockExcel;
use Intouch\Framework\Dao\Queryable;

trait VWStockExcelDaoT
{
    public function BuscarStock() {
        $qry = (new Queryable())
                ->From(VWStockExcel::class)
                ->OrderBy('Fecha_Llegada desc');

        $result     =    $this->Query(
            query: $qry
        );

        return $result;
    }
}
