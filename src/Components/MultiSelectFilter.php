<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\MultiSelectFilterConfig;
use Nayjest\Grids\EloquentDataProvider;

class MultiSelectFilter extends Filter
{
    public function __construct($columnName, $relation, $options)
    {
        $config = new MultiSelectFilterConfig();
        $config->setOptions($options);
        $name = "multi-select-" . (!empty($relation) ? $relation . "." . $columnName : $columnName);
        $config->setName($name);
        parent::__construct($config);
        $this->setDefaultFilteringFunc($columnName, $relation);
    }

    private function setDefaultFilteringFunc($columnName, $relation = null)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            if (!empty($val) && is_array($val) && !empty($val[0])) {
                if ($relation) {
                    $builder->whereHas($relation, function ($query) use ($columnName, $val) {
                        $query->whereIn($columnName, $val);
                    });
                } else {
                    $builder->whereIn($columnName, $val);
                }
            }
            return $this;
        });
        return $this;
    }
}
