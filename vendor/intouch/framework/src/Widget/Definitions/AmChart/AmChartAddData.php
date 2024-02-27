<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

class AmChartAddData {

    public $_LABEL = '';
    public $_VALUE = '';
    public $_DATETIMEFORMAT = false;

    public function __construct(
        public string   $LabelName = '',
        public string   $ValueName = '',
        public bool     $DateTime  = false
    )
    {
        $this->_LABEL = $LabelName;
        $this->_VALUE = $ValueName;
        $this->_DATETIMEFORMAT  = $DateTime;
    }
}