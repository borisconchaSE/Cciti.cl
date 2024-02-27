<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Chart;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\Definitions\AmChart5\Axis\XAxis;
use Intouch\Framework\Widget\Definitions\AmChart5\Basic;
use Intouch\Framework\Widget\Definitions\AmChart5\Data\Data;
use Intouch\Framework\Widget\Definitions\AmChart5\Data\DataCollection;
use Intouch\Framework\Widget\Definitions\AmChart5\Data\DataItem;

abstract class Chart extends Basic {

    public DataCollection $Data;

    public function __construct(
        string  $Key,
        array   $Replace,
        ?array  $Data = null,
        string  $Category = '',
    ) {

        // Convertir "Data" para que sea usable por los graficos
        //
        $this->Data = new DataCollection(
            DataItems   :   $Data,
            Category    :   $Category,
        );

        parent::__construct(
            Key     : $Key,
            Replace : $Replace,            
        );

    }

}