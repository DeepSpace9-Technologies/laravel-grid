<?php

namespace Nayjest\Grids;

use Event;
use Cache;
use Illuminate\Support\Facades\Request;
use Nayjest\Grids\Components\Column;
use Nayjest\Grids\Components\FormAttributes;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use View;
use Closure;

class Grid
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    const EVENT_PREPARE = 'grid.prepare';
    const EVENT_CREATE = 'grid.create';
    const OPERATOR_LIKE = 'like';
    const OPERATOR_EQ = '=';
    const OPERATOR_NOT_EQ = '<>';
    const OPERATOR_GT = '>';
    const OPERATOR_LS = '<';
    const OPERATOR_LSE = '<=';
    const OPERATOR_GTE = '>=';

    /** @var GridConfig */
    protected $config;

    /** @var bool */
    protected $prepared = false;

    /** @var  Sorter */
    protected $sorter;

    /** @var  GridInputProcessor */
    protected $input_processor;

    protected $filtering;

    /** @var  FilterGridWithDateRange */
    protected $filterGridByDateRange;
    private $actions = [];
    protected $hiddenColumns = [];
    protected $sort = [];


    public function __construct(GridConfig $config)
    {
        $this->config = $config;
        if ($config->getName() === null) {
            $this->provideName();
        }
        $this->setDefaultPageSize(10);
        $this->setDefaultSort(['id' => 'desc']);
        event(self::EVENT_CREATE, $this);
    }

    /**
     * @return string
     */
    protected function getMainTemplate()
    {
        return $this->config->getMainTemplate();
    }

    public function setName($name)
    {
        $this->config->setName($name);
        return $this;
    }

    public function setDefaultPageSize($size)
    {
        $this->config->setPageSize($size);
        return $this;
    }

    public function setDefaultGridDateRangeFilter($columnName, $dateRange)
    {
        $this->config->setGridDateRangeFilter($columnName, $dateRange);
        return $this;
    }

    public function setDefaultSort($sortArray)
    {
        $this->sort = $sortArray;
        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    public function addColumn($name, $label = null)
    {
        $column = new Column($this, $name, $label);
        $this->config->addColumn($column->getConfig());
        return $column;
    }

    public function addHiddenColumn($name)
    {
        $this->hiddenColumns[] = $name;
        return $this;
    }

    public function addAction($name, $href, $destination, $attributes = [], Closure $callback = null)
    {
        $formAttributes = new FormAttributes();
        $formAttributes->setHref($href)->setName($name)->setDataDestination($destination);
        foreach ($attributes as $key => $value) {
            $formAttributes->setAttribute($key, $value);
        }
        if (!empty($callback)) {
            call_user_func($callback, $formAttributes);
        }

        $this->actions[] = $formAttributes->getAttributes();
    }


    public function prepare()
    {
        if ($this->prepared === true) {
            return;
        }
        $cfg = $this->config;
        $cfg->getDataProvider()
            ->setPageSize(
                $cfg->getPageSize()
            )
            ->setCurrentPage(
                $this->getInputProcessor()->getValue('page', 1)
            );
        $this->getConfig()->prepare();
        $this->getFiltering()->apply();
        $this->prepareColumns();
        $this->getSorter()->apply();
        $this->getFilterGridByDateRange()->apply();
        event(self::EVENT_PREPARE, $this);
        $this->prepared = true;
    }

    protected function initializeComponents()
    {
        $this->getConfig()->initialize($this);
    }

    protected function prepareColumns()
    {
        if ($this->needToSortColumns()) {
            $this->sortColumns();
        }
    }

    /**
     * Provides unique name for each grid on the page
     *
     * @return null
     */
    protected function provideName()
    {
        $bt_len = 10;
        $backtrace = debug_backtrace(null, $bt_len);
        $str = '';
        for ($id = 2; $id < $bt_len; $id++) {
            $trace = isset($backtrace[$id]) ? $backtrace[$id] : [];
            if (empty($trace['class']) || !$this instanceof $trace['class']) {
                # may be closure
                if (isset($trace['file'], $trace['line'])) {
                    $str .= $trace['file'] . $trace['line'];
                }
            }
        }
        $this->config->setName(substr(md5($str), 0, 16));
    }

    /**
     * Returns true if columns must be sorted.
     *
     * @return bool
     */
    protected function needToSortColumns()
    {
        foreach ($this->config->getColumns() as $column) {
            if ($column->getOrder() !== 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sorts columns according to its order.
     */
    protected function sortColumns()
    {
        $this->config->getColumns()->sort(function (FieldConfig $a, FieldConfig $b) {
            return $a->getOrder() > $b->getOrder();
        });
    }

    /**
     * Returns data sorting manager.
     *
     * @return Sorter
     */
    public function getSorter()
    {
        if (null === $this->sorter) {
            $this->sorter = new Sorter($this);
        }
        return $this->sorter;
    }

    public function getFilterGridByDateRange()
    {
        if (null === $this->filterGridByDateRange) {
            $this->filterGridByDateRange = new FilterGridWithDateRange($this);
        }
        return $this->filterGridByDateRange;
    }

    /**
     * Returns instance of GridInputProcessor.
     *
     * @return GridInputProcessor
     */
    public function getInputProcessor()
    {
        if (null === $this->input_processor) {
            $this->input_processor = new GridInputProcessor($this);
        }
        return $this->input_processor;
    }

    /**
     * @return GridConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getViewData()
    {
        return [
            'grid' => $this,
            'data' => $this->config->getDataProvider(),
            'template' => $this->config->getTemplate(),
            'columns' => $this->config->getColumns()
        ];
    }

    /**
     * Renders grid.
     *
     * @return View|string
     */
    public function render()
    {
        $this->sort();
        $header = new HtmlTag($this->config->getComponentByName(THead::NAME));
        $header->setBulkActions($this->actions);
        $header->setDefaultGridDateRangeFilter($this->config->getGridDateRangeFilter());
        $header->setDefaultComponents();
        $this->initializeComponents();
        $key = $this->getInputProcessor()->getUniqueRequestId();
        $caching_time = $this->config->getCachingTime();
        if ($caching_time && ($output = Cache::get($key))) {
            return $output;
        } else {
            $this->prepare();
            $provider = $this->config->getDataProvider();
            $provider->reset();
            $output = View::make(
                $this->getMainTemplate(),
                $this->getViewData()
            )->render();
            if ($caching_time) {
                Cache::put($key, $output, $caching_time);
            }
            return $output;
        }
    }

    /**
     * Returns footer component.
     *
     * @return TFoot|null
     */
    public function footer()
    {
        return $this->getConfig()->getComponentByName('tfoot');
    }

    /**
     * Returns header component.
     *
     * @return THead|null
     */
    public function header()
    {
        return $this->getConfig()->getComponentByName('thead');
    }

    /**
     * Returns data filtering manager.
     *
     * @return Filtering
     */
    public function getFiltering()
    {
        if ($this->filtering === null) {
            $this->filtering = new Filtering($this);
        }
        return $this->filtering;
    }

    /**
     * Renders grid object when it is treated like a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    private function sort()
    {
        $name = empty($this->config->getName()) ? 'Default-Name' : $this->config->getName();
        if (empty(Request::get($name)['sort'])) {
            Request::merge([
                $name => [
                    'sort' => $this->sort
                ]
            ]);
        } else {
            $sort = [];
            foreach (Request::get($name)['sort'] as $column => $value) {
                if (!empty($value)) {
                    $sort[$column] = $value;
                }
            }
            if (empty($sort)) {
                $sort = $this->sort;
            }
            $filters = Request::get($name);
            $filters['sort'] = $sort;
            Request::merge([
                $name => $filters
            ]);
        }
    }

    public function getBuilder()
    {
        $this->prepare();
        return $this->getConfig()->getDataProvider()->getBuilder();
    }
}
