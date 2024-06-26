<?php
namespace Nayjest\Grids;

use Illuminate\Database\Eloquent\Builder;
use Event;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class EloquentDataProvider extends DataProvider
{
    protected $collection;

    protected $paginator;

    /** @var  $iterator \ArrayIterator */
    protected $iterator;

    /**
     * Constructor.
     *
     * @param Builder $src
     */
    public function __construct(Builder $src)
    {
        parent::__construct($src);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->getIterator()->rewind();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $paginator = $this->getPaginator();
            if (version_compare(Application::VERSION, '5', '<')) {
                $this->collection = $paginator->getCollection();
            } else {
                $this->collection = Collection::make(
                    $this->getPaginator()->items()
                );
            }
        }
        return $this->collection;
    }

    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->src->paginate($this->page_size);
        }
        return $this->paginator;
    }

    /**
     * @return \Illuminate\Pagination\Factory
     */
    public function getPaginationFactory()
    {
        return $this->src->getQuery()->getConnection()->getPaginator();
    }

    protected function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = $this->getCollection()->getIterator();
        }
        return $this->iterator;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->src;
    }

    public function getRow()
    {
        if ($this->index < $this->count()) {
            $this->index++;
            $item = $this->iterator->current();
            $this->iterator->next();
            $row = new EloquentDataRow($item, $this->getRowId());
            event(self::EVENT_FETCH_ROW, [$row, $this]);
            return $row;
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getCollection()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($fieldName, $direction)
    {
        $this->src->orderBy($fieldName, $direction);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($fieldName, $operator, $value)
    {
        $this->src->where($fieldName, $operator, $value);
        return $this;
    }

    public function dateTimeRangeFilter($relationColumn, array $value)
    {
        if(count($relationColumn) > 1){
            $this->src->whereHas($relationColumn[0], function ($query) use ($relationColumn, $value) {
                $query->whereBetween($relationColumn[1], $value);
            });
        }else{
            $this->src->whereBetween($relationColumn[0], $value);
        }
        return $this;
    }
}
