<?php

namespace Nayjest\Grids\Core;

use Nayjest\Grids\Core\Filter;
use Nayjest\Grids\Core;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\EloquentDataProvider;

class SearchFilter extends Filter
{
    public function __construct($columnName, $relation, $operator)
    {
        $config = new FilterConfig();
        $config->setName($relation.".".$columnName);
        $config->setOperator($operator);
        parent::__construct($config);
        if ($relation) {
            $this->setDefaultFilteringFunc($columnName, $relation);
        }
    }

    private function setDefaultFilteringFunc($columnName, $relation)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            $builder->whereHas($relation, function ($query) use ($columnName, $val) {
                $query->whereIn($columnName, explode(',', trim($val)));
            });
        });
        return $this;
    }
}
