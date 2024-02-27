<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class Empresa
{
    use EmpresaT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdEmpresa = 0;
	public string $Descripcion = '';
	public int $IdCliente = 0;
	public string $Logo = '';
	public string $Alias = '';

    function __construct()
    {
    }
}