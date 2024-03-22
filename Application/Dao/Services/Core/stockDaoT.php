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

        $result     =    $this->Query(
            query: $qry
        );

        return $result;
    }

    public function BuscarEntregado() {
        $qry = (new Queryable())
                ->From(stock::class)
                ->With(empresa::class)
                ->With(marca::class)
                ->Where('estado_stock = ?',"Entregado")
                ->OrderBy('id_stock desc')
                ->Top(25);

        $result     =    $this->Query(
            query: $qry
        );

        return $result;
    }
}
