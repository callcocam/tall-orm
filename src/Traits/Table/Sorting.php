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
     * The initial field to be sorting by.
     *
     * @var string
     */
    public $field = 'created_at';

    /**
     * The initial direction to sort.
     *
     * @var bool
     */
    public $direction = 'desc';

    /**
     * @param $attribute
     */
    public function sort($attribute)
    {
        if ($this->field !== $attribute) {
            $this->direction = 'asc';
        } else {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        }

        $this->field = $attribute;
    }

    /**
     *
     * @return string
     */
    protected function getSortField(): string
    {
        return $this->field;
    }
}
