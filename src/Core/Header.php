<?php

namespace Nayjest\Grids\Core;

use Illuminate\Support\Facades\Log;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Core\Component;

class Header extends Component
{
    private $actions;
    private $defaultDateRange;

    public function __construct($config = null)
    {
        if (empty($config)) {
            $config = new THead();
        }
        parent::__construct($config);
    }

    public function setBulkActions($actions)
    {
        $this->actions = $actions;
    }

    public function setDefaultGridDateRangeFilter($defaultDateRange)
    {
        $this->defaultDateRange = $defaultDateRange;
    }

    public function setDefaultComponents()
    {
        $headerTag = $this->createHtmlTag()->addClass("row");
        $leftTag = $this->createHtmlTag()->addClass("col-xs-6")
            ->addRecordsPerPage([10, 20, 50, 100, 200])
            ->addShowingRecords();
        if(!empty($this->defaultDateRange)){
            $leftTag->addRecordsPerMonth([3, 6, 9, 12, 15]);
        }

        $rightTag = $this->createHtmlTag()->addClass("col-xs-6 text-right")
            ->addActions($this->actions)
            ->addResetButton()
            ->addExcelExport('excel-data-' . date('d-m-Y-h-i-s'))
            ->addCsvExport('csv-data-' . date('d-m-Y-h-i-s'));
        // ->addColumnsHider();

        $headerTag->getConfig()->addComponent($leftTag->getConfig());
        $headerTag->getConfig()->addComponent($rightTag->getConfig());

        $this->config->addComponent($headerTag->getConfig());
    }
}
