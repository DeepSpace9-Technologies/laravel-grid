<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\FilterConfig;

class Filter
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

    public function setFilteringFunc($function)
    {
        $this->config->setFilteringFunc($function);
    }
}

