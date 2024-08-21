<?php

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\HtmlTag as NayTag;
use Spatie\Html\Html;

class HtmlTag extends RenderableRegistry
{
    protected $tag_name;

    protected $content;

    /**
     * HTML tag attributes.
     * Keys are attribute names and values are attribute values.
     * @var array
     */
    protected $attributes = [];

    protected $config;

    public function __construct($config = null)
    {
        $this->config = $config;
    }

    /**
     * Returns component name.
     * If empty, tag_name will be used instead
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name ?: $this->getTagName();
    }

    /**
     * Allows to specify HTML tag.
     *
     * @param string $name
     * @return $this
     */
    public function setTagName($name)
    {
        $this->tag_name = $name;
        return $this;
    }

    /**
     * Returns HTML tag.
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->tag_name ?: $this->suggestTagName();
    }

    /**
     * Suggests tag name by class name.
     *
     * @return string
     */
    private function suggestTagName()
    {
        $class_name = get_class($this);
        $parts = explode('\\', $class_name);
        $base_name = array_pop($parts);
        return ($base_name === 'HtmlTag') ? 'div' : strtolower($base_name);
    }

    /**
     * Sets content (html inside tag).
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns html inside tag.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Returns html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Renders opening tag.
     *
     * @return string
     */
    public function renderOpeningTag()
    {
        $html = app(Html::class);
        $element = $html->element($this->getTagName())->attributes($this->getAttributes());
        return (string) $element->open();
    }

    /**
     * Renders closing tag.
     *
     * @return string
     */
    public function renderClosingTag()
    {
        $html = app(Html::class);
        $element = $html->element($this->getTagName())->attributes($this->getAttributes());
        return (string) $element->close();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->getTemplate()) {
            $inner = $this->renderTemplate();
        } else {
            $this->is_rendered = true;
            $inner = $this->renderOpeningTag()
                . $this->renderComponents(self::SECTION_BEGIN)
                . $this->getContent()
                . $this->renderComponents(null)
                . $this->renderComponents(self::SECTION_END)
                . $this->renderClosingTag();
        }
        return $this->wrapWithOutsideComponents($inner);
    }

    public function createHtmlTag()
    {
        return new NayTag();
    }

    public function addClass($className)
    {
        $attributes = $this->getAttributes();
        $class = array_key_exists("class", $attributes) ? $attributes['class'] : '';
        $class = $class . " " . $className;
        $attributes['class'] = $class;
        $this->setAttributes($attributes);
        return $this;
    }

    public function addExcelExport($fileName)
    {
        $excelExport = (new ExcelExport())->setFileName($fileName);
        $this->addComponent($excelExport);
        return $this;
    }

    public function addCsvExport($fileName)
    {
        $excelExport = (new CsvExport())->setFileName($fileName);
        $this->addComponent($excelExport);
        return $this;
    }

    public function addColumnsHider()
    {
        $columnsHider = new ColumnsHider;
        $this->addComponent($columnsHider);
        return $this;
    }

    public function addRecordsPerPage($variants = [])
    {
        $recordsPerPage = new RecordsPerPage;
        if ($variants) {
            $recordsPerPage->setVariants($variants);
        }
        $this->addComponent($recordsPerPage);
        return $this;
    }

    public function addShowingRecords()
    {
        $showingRecords = new ShowingRecords;
        $this->addComponent($showingRecords);
        return $this;
    }

    public function addResetButton()
    {
        $resetButton = (new NayTag())
            ->setContent('<i class="fa fa-refresh" aria-hidden="true"></i> Reset')
            ->setTagName('button')
            ->setAttributes([
                'type' => 'button',
                'class' => 'btn btn-success btn-sm grid-reset',
            ]);
        $this->addComponent($resetButton);
        return $this;
    }

    public function addAction($action)
    {
        $action['class'] = empty($action['class']) ? 'action' : $action['class'] . ' action';
        $actionButtonConfig = $this->createHtmlTag();
        $content = (empty($action['href']) && !empty($action['button-class'])) ? '<button class="btn btn-small ' . $action['button-class'] . '" type="button">' . $action['name'] . '</button>' : '<a href="' . $action['href'] . '">' . $action['name'] . '</a>';
        $actionButtonConfig->setContent($content)
            ->setTagName('li')
            ->setAttributes($action);
        $this->addComponent($actionButtonConfig);
    }

    public function addActions($actions)
    {
        if (!empty($actions)) {
            $dropdownWrapper = $this->createHtmlTag()->addClass('btn-group mr-5');
            $dropdownWrapperConfig = $dropdownWrapper;
            $dropdownButtonConfig = $this->createHtmlTag();
            $dropdownButtonConfig->setContent('Actions <span class="fa fa-caret-down"></span>')
                ->setTagName('button')
                ->setAttributes([
                    'type' => 'button',
                    'class' => 'btn btn-sm btn-default bg-purple dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ]);

            $dropdown = $this->createHtmlTag()->addClass("dropdown-menu bulk-actions");
            foreach ($actions as $action) {
                $dropdown->addAction($action);
            }
            $dropdownConfig = $dropdown;
            $dropdownConfig->setTagName('ul');

            // $dropdownWrapperConfig->setTagName('span');
            $dropdownWrapperConfig->addComponent($dropdownButtonConfig);
            $dropdownWrapperConfig->addComponent($dropdownConfig);
            $this->addComponent($dropdownWrapperConfig);
        }
        return $this;
    }

    public function addRecordsPerMonth($defaultDateRange)
    {
        if (!empty($defaultDateRange)) {
            $this->addComponent(new RecordsPerMonth());
            return $this;
        }
        return $this;
    }

    //Default Components
    public function setBulkActions($actions)
    {
        $this->actions = $actions;
    }

    public function setDefaultGridDateRangeFilter($defaultDateRange)
    {
        $this->defaultDateRange = $defaultDateRange;
    }

    public function setDefaultComponents()
    {
        $headerTag = $this->createHtmlTag()->addClass(!empty($this->defaultDateRange) ? "row mt-5" : "row");
        $leftTag = $this->createHtmlTag()->addClass(!empty($this->defaultDateRange) ? "col-xs-8" : "col-xs-6")
            ->addRecordsPerPage([10, 20, 50, 100, 200])
            ->addShowingRecords();
        if (!empty($this->defaultDateRange)) {
            $leftTag->addRecordsPerMonth([3, 6, 9, 12, 15]);
        }

        $rightTag = $this->createHtmlTag()->addClass(!empty($this->defaultDateRange) ? "col-xs-4 text-right" : "col-xs-6 text-right")
            ->addActions($this->actions)
            ->addResetButton()
            ->addExcelExport('excel-data-' . date('d-m-Y-h-i-s'))
            ->addCsvExport('csv-data-' . date('d-m-Y-h-i-s'));
        // ->addColumnsHider();

        $headerTag->addComponent($leftTag);
        $headerTag->addComponent($rightTag);

        $this->config->addComponent($headerTag);
    }
}

