<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\tipoproducto;
use Intouch\Framework\Dao\Queryable;

trait tipoproductoDaoT
{

    public function BuscarTipoGenerales() {

        $qry = (new Queryable())
                ->From(tipoproducto::class)
                ->Where('idTipoProducto not in (?,?,?,?,?)',"1","4","7","9","11");
        return $this->Query(
            query: $qry
        );
    }
    public function BuscarTipoGastos() {
        $qry = (new Queryable())
                ->From(tipoproducto::class)
                ->Where("idTipoProducto in (?,?,?,?)","4","7","9","11");
        return $this->Query(
            query: $qry
        );
    }
}
