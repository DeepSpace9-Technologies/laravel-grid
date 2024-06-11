<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\RecordsPerMonth;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\GridConfig;

class Component
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function createHtmlTag()
    {
        return new HtmlTag();
    }
}
