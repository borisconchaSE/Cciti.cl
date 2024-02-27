<?php
namespace Application\Dao\Services\Core;
use Application\Dao\Entities\Core\empresa;
use Application\Dao\Entities\Core\stock;
use Application\Dao\Entities\Core\marca;
use Intouch\Framework\Dao\Queryable;

trait stockDaoT
{
    public function BuscarStock() {
        $qry = (new Queryable())
                ->From(stock::class)
                ->With(empresa::class)
                ->With(marca::class)
                ->Where('estado_stock = ?',"En Stock")
                ->OrderBy('id_stock desc');

                // $qry = (new Queryable())
                // ->From(stock::class)
                // ->With(empresa::class)
                // ->With(marca::class)
                // ->Where('estado_stock = ? AND Fecha_asignacion BETWEEN DATE(?) AND DATE(?)',"En Stock","2023-01-01","2024-12-31")
                // ->OrderBy('Fecha_asignacion desc')
                // ->Top(100);

        $result     =    $this->Query(
            query: $qry
        );

        return $result;
    }
}
