<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Table;

use Tall\Orm\Core\Table\Traits\TColumn;
use Illuminate\Support\Str;

/**
 * Class Column.
 */
class Column
{
    use TColumn;

    protected $fk;
    
    protected $db;

    public function __construct($label, $name=null)
    {
        $this->name     =   $name ?? Str::slug($label, '_');
        $this->label     =   $label;
        $this->key      = $this->name;
    }

    public static function make($label, $name=null)
    {
        return new static($label, $name);
    }

    public function make_field_attributes($make_field_attributes=[])
    {
        $this->attributes = $make_field_attributes;

        return $this;
    }

    public function make_field_options($make_field_options=[])
    {
        $this->options = $make_field_options;

        return $this;
    }

    public function make_field_db($make_field_db)
    {
        $this->db = $make_field_db;

        return $this;
    }

    public function make_field_fk($make_field_fk)
    {
        $this->fk = $make_field_fk;

        return $this;
    }
}
