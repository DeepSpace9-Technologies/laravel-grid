<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Filter;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\EloquentDataProvider;

class SearchFilter extends Filter
{
    public function __construct($columnName, $relation, $operator)
    {
        $config = new FilterConfig();
        $name = !empty($relation) ? $relation . "." . $columnName : $columnName;
        $config->setName($name);
        $config->setOperator($operator);
        parent::__construct($config);
        $this->setDefaultFilteringFunc($columnName, $relation);
    }

    private function setDefaultFilteringFunc($columnName, $relation)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($columnName, $relation) {
            $builder = $dp->getBuilder();
            $values = array_map('trim', explode(',', $val));
            if ($relation) {
                $builder->whereHas($relation, function ($query) use ($columnName, $values) {
                    $query->where(function ($q) use ($columnName, $values) {
                        foreach ($values as $value) {
                            $q->orWhere($columnName, 'like', "%$value%");
                        }
                    });
                });
            } else {
                $builder->where(function ($q) use ($columnName, $values) {
                    foreach ($values as $value) {
                        $q->orWhere($columnName, 'like', "%$value%");
                    }
                });
            }
        });
        return $this;
    }
}
