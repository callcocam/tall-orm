<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Traits\Table;

/**
 * Trait Sorting.
 */
trait Sorting
{
    /**
     * Sorting.
     * The initial sortField to be sorting by.
     *
     * @var string
     */
    public $sortField = 'sort';

    /**
     * The initial direction to sort.
     *
     * @var bool
     */
    public $direction = 'direction';

    /**
     * @param $attribute
     */
    public function sort($attribute)
    {
        if ($sort = data_get($this->filters, $this->sortField)) {

            if ($sort !== $attribute) {

                data_set($this->filters, $this->direction,  'asc');

            } else {

                $direction = data_get($this->filters, $this->direction) === 'asc' ? 'desc' : 'asc';

                data_set($this->filters, $this->direction,  $direction);

            }

        } else {

            data_set($this->filters, $this->direction,  'asc');

        }
            
        data_set($this->filters, $this->sortField,  $attribute);

    }

    /**
     *
     * @return string
     */
    protected function getSortField(): string
    {
        return data_get($this->filters, $this->sortField, 'created_at');
    }

    /**
     *
     * @return string
     */
    protected function getDirection(): string
    {
        return data_get($this->filters, $this->direction, 'asc');
    }
}
