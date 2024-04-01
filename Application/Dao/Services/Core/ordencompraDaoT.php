<?php
namespace Application\Dao\Services\Core;
use Application\Dao\Entities\Core\ordenCompra;
use Application\Dao\Entities\Core\proveedor;
use Application\Dao\Entities\Core\estadoOC;
use Application\Dao\Entities\Core\estadoFC;
use Application\Dao\Entities\Core\empresa;
use Application\Dao\Entities\Core\tipoproducto;
use Intouch\Framework\Dao\Queryable;

trait ordencompraDaoT
{
    public function BuscarCompras() {
        $qry = (new Queryable())
                ->From(ordenCompra::class)
                ->With(proveedor::class)
                ->With(estadoOC::class)
                ->With(estadoFC::class)
                ->With(empresa::class)
                ->Where('idTipoProducto = ?',"1")
                ->OrderBy('Fecha_compra desc')
                ->Top(25);

        return $this->Query(
            query: $qry
        );
    }
    public function BuscarComprasGenerales() {
        $qry = (new Queryable())
                ->From(ordenCompra::class)
                ->With(proveedor::class)
                ->With(estadoOC::class)
                ->With(estadoFC::class)
                ->With(empresa::class)
                ->With(tipoproducto::class)
                ->Where('ordencompra.idTipoProducto not in (?,?,?)',"1","4","7")
                ->OrderBy('Fecha_compra desc');

        return $this->Query(
            query: $qry
        );
    }
    public function BuscarGastos() {
        $qry = (new Queryable())
                ->From(ordenCompra::class)
                ->With(proveedor::class)
                ->With(estadoOC::class)
                ->With(estadoFC::class)
                ->With(empresa::class)
                ->With(tipoproducto::class)
                ->Where('ordencompra.idTipoProducto in (?,?)',"4","7")
                ->OrderBy('Fecha_compra desc');

        return $this->Query(
            query: $qry
        );
    }
}
