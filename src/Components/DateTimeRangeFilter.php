<?php

namespace Nayjest\Grids\Components;

use DateTime;
use DateTimeZone;
use Nayjest\Grids\DateTimeRangeFilterConfig;
use Nayjest\Grids\EloquentDataProvider;

class DateTimeRangeFilter extends Filter
{
    public function __construct($columnName, $relation, $operator)
    {
        $config = new DateTimeRangeFilterConfig();
        $config->setName($relation . "." . $columnName);
        $config->setLabel("test");
        $config->setOperator($operator);
        $config->set("config", $this->getPickerConfig());
        parent::__construct($config);
        $this->setDefaultFilteringFunc($columnName, $relation);
    }

    private function getPickerConfig()
    {
        return [
            "autoClose" => true
        ];
    }

    private function setDefaultFilteringFunc($columnName, $relation)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            $dates = explode(" to ", $val);
            $dates[1] = date('Y-m-d', strtotime($dates[1] . ' +1 day'));
            $dates[0] = (new DateTime($dates[0], new DateTimeZone("Asia/Kolkata")))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
            $dates[1] = (new DateTime($dates[1], new DateTimeZone("Asia/Kolkata")))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
            if (empty($relation)) {
                $builder->whereBetween($columnName, [$dates[0], $dates[1]]);
            } else {
                $builder->whereHas($relation, function ($query) use ($columnName, $dates) {
                    $query->whereBetween($columnName, [$dates[0], $dates[1]]);
                });
            }
        });

        return $this;
    }
}
