<?php
namespace Nayjest\Grids;

class MultiSelectFilterConfig extends FilterConfig
{
    protected $template = '*.multiselect';

    protected $options = [];

    /**
     * Returns option items of html select tag.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets option items for html select tag.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
}

