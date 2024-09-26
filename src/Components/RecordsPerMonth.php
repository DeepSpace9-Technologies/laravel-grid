<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;

/**
 * Class RecordsPerMonth
 *
 * The component renders control
 * for switching count of records displayed per Month.
 *
 * @package Nayjest\Grids\Components
 */
class RecordsPerMonth extends RenderableComponent
{

    protected $name = 'records_per_month';

    protected $monthCount;

    protected $template = '*.components.records_per_month';

    private $columnName;

    public function getConfig()
    {
        return $this->grid->getConfig();
    }

    /**
     * Returns name of related input.
     *
     * @return string
     */
    public function getInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();
        return "{$key}[filters][records_per_month]";
    }

    /**
     * Returns current value from input.
     * Default grids pre-configured page size will be returned if there is no input.
     *
     * @return int|null
     */
    public function getValue()
    {
        $from_input = $this
            ->grid
            ->getInputProcessor()
            ->getFilterValue('records_per_month');
        if ($from_input === null) {
            return $this->grid->getConfig()->getGridDateRangeFilter();
        } else {
            return $from_input;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if (is_array($this->getValue())) {
            $columnName = explode('.', $this->getValue()[0]);
            if (count($columnName) > 1) {
                setcookie("columnName", $columnName[1], time() + 60 * 60 * 24 * 7);
            } else {
                setcookie("columnName", $columnName[0], time() + 60 * 60 * 24 * 7);
            }
            $this->grid->getConfig()->getDataProvider()->setGridDefaultDateRangeFilter($this->getValue()[0], trim($this->getValue()[1]));
        } else {
            if (isset($_COOKIE["columnName"])) {
                $this->grid->getConfig()->getDataProvider()->setGridDateRangeFilter($_COOKIE["columnName"], trim($this->getValue()));
            }

        }
    }
}
