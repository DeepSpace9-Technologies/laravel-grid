<?php

namespace Nayjest\Grids\Core;

use Nayjest\Grids\Components\HtmlTag as NayTag;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\RecordsPerMonth;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Core\HtmlTag;
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
        $htmlTag = new HtmlTag();
        return $htmlTag;
    }

    public function addExcelExport($fileName)
    {
        $excelExport = (new ExcelExport())->setFileName($fileName);
        $this->config->addComponent($excelExport);
        return $this;
    }

    public function addCsvExport($fileName)
    {
        $excelExport = (new CsvExport())->setFileName($fileName);
        $this->config->addComponent($excelExport);
        return $this;
    }

    public function addColumnsHider()
    {
        $columnsHider = new ColumnsHider;
        $this->config->addComponent($columnsHider);
        return $this;
    }

    public function addRecordsPerPage($variants = [])
    {
        $recordsPerPage = new RecordsPerPage;
        if ($variants) {
            $recordsPerPage->setVariants($variants);
        }
        $this->config->addComponent($recordsPerPage);
        return $this;
    }

    public function addShowingRecords()
    {
        $showingRecords = new ShowingRecords;
        $this->config->addComponent($showingRecords);
        return $this;
    }

    public function addResetButton()
    {
        $resetButton = (new NayTag())
            ->setContent('<i class="fa fa-refresh" aria-hidden="true"></i> Reset')
            ->setTagName('button')
            ->setAttributes([
                'type' => 'button',
                'class' => 'btn btn-success btn-sm grid-reset',
            ]);
        $this->config->addComponent($resetButton);
        return $this;
    }

    public function addAction($action)
    {
        $action['class'] = empty($action['class']) ? 'action' : $action['class'].' action';
        $actionButtonConfig = $this->createHtmlTag()->getConfig();
        $content = (empty($action['href']) && !empty($action['button-class'])) ? '<button class="btn btn-small '.$action['button-class'].'" type="button">'.$action['name'].'</button>' : '<a href="'.$action['href'].'">'.$action['name'].'</a>';
        $actionButtonConfig->setContent($content)
            ->setTagName('li')
            ->setAttributes($action);
        $this->config->addComponent($actionButtonConfig);
    }

    public function addActions($actions)
    {
        if(!empty($actions)){
            $dropdownWrapper = $this->createHtmlTag()->addClass('btn-group mr-5');
            $dropdownWrapperConfig = $dropdownWrapper->getConfig();
            $dropdownButtonConfig = $this->createHtmlTag()->getConfig();
            $dropdownButtonConfig->setContent('Actions <span class="fa fa-caret-down"></span>')
                ->setTagName('button')
                ->setAttributes([
                    'type' => 'button',
                    'class' => 'btn btn-sm btn-default bg-purple dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ]);

            $dropdown =  $this->createHtmlTag()->addClass("dropdown-menu bulk-actions");
            foreach ($actions as $action) {
                $dropdown->addAction($action);
            }
            $dropdownConfig = $dropdown->getConfig();
            $dropdownConfig->setTagName('ul');

            // $dropdownWrapperConfig->setTagName('span');
            $dropdownWrapperConfig->addComponent($dropdownButtonConfig);
            $dropdownWrapperConfig->addComponent($dropdownConfig);
            $this->config->addComponent($dropdownWrapperConfig);
        }
        return $this;
    }

    public function addRecordsPerMonth($defaultDateRange)
    {
        if (!empty($defaultDateRange)) {
            $this->config->addComponent(new RecordsPerMonth());
            return $this;
        }
        return $this;
    }
}
