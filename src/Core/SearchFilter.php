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
        $name = !empty($relation) ? $relation.".".$columnName : $columnName;
        $config->setName($name);
        $config->setOperator($operator);
        parent::__construct($config);
        $this->setDefaultFilteringFunc($columnName, $relation);
    }

    private function setDefaultFilteringFunc($columnName, $relation)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            $val = str_replace(' ', '', $val);
            if ($relation) {
                $builder->whereHas($relation, function ($query) use ($columnName, $val) {
                    $query->whereIn($columnName, explode(',', trim(str_replace(' ', '', $val))));
                });
            } else {
                $builder->whereIn($columnName, explode(',', trim(str_replace(' ', '', $val))));
            }
        });
        return $this;
    }
}
