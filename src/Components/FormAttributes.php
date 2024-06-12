<?php

namespace Nayjest\Grids\Components;

class FormAttributes
{
    protected $attributes=[];

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($key,$value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function setHref($value)
    {
        $this->attributes['href'] = $value;
        return $this;
    }

    public function setName($value)
    {
        $this->attributes['name'] = $value;
        return $this;
    }

    public function setDataDestination($value)
    {
        $this->attributes['data-destination'] = $value;
        return $this;
    }

    public function setSelectionMandatory()
    {
        $this->attributes['selection-mandatory'] =true;
        return $this;
    }

}