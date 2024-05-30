<?php

namespace Nayjest\Grids\Core;

use Nayjest\Grids\Core\Component;
use Nayjest\Grids\Components\HtmlTag as NayTag;

class HtmlTag extends Component
{
    public function __construct()
    {
        $config = new NayTag;
        parent::__construct($config);
    }

    public function addClass($className)
    {
        $attributes = $this->getConfig()->getAttributes();
        $class = array_key_exists("class", $attributes) ? $attributes['class'] : '';
        $class = $class . " " . $className;
        $attributes['class'] = $class;
        $this->getConfig()->setAttributes($attributes);
        return $this;
    }
}
