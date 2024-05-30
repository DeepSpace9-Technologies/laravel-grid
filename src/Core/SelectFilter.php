<?php

namespace Nayjest\Grids\Core;

use Illuminate\Support\Facades\Log;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Core\Filter;
use Nayjest\Grids\SelectFilterConfig;
use Nayjest\Grids\EloquentDataProvider;

class SelectFilter extends Filter
{
    public function __construct($columnName, $relation, $options)
    {
        $config = new SelectFilterConfig();
        $config->setOptions($options);
        parent::__construct($config);
        $this->setDefaultFilteringFunc($columnName, $relation);
    }

    private function setDefaultFilteringFunc($columnName, $relation = null)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            if ($relation) {
                $builder->whereHas($relation, function ($query) use ($columnName, $val) {
                    $query->where($columnName, $val);
                });
            } else {
                $builder->where($columnName, $val);
            }
        });
        return $this;
    }
}
