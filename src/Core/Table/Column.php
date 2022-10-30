<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Table;

use Tall\Orm\Traits\Kill;

/**
 * Class Column.
 */
class Column
{
    use Kill;

   
    /**
     * @var string
     */
    protected $name;
   
    /**
     * @var bool
     */
    protected $searchable = true;

    /**
     * @var bool
     */
    protected $sortable = true;


    /**
     * Column constructor.
     *
     * @param string $attribute
     */
    public function __construct(string $attribute)
    {
      $this->name = $attribute;
    }

    /**
     * @param string $attribute
     *
     * @return Column
     */
    public static function make(string $attribute): Column
    {
        return new static($$attribute);
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function __get($name)
    {
        return $this->{$name};
    }
}
