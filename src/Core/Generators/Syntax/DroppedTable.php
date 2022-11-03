<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Generators\Syntax;

class DroppedTable
{
    /**
     * Get string for dropping a table
     *
     * @param      $tableName
     * @param null $connection
     *
     * @return string
     */
    public function drop($tableName, $connection = null)
    {
        if (!is_null($connection)) $connection = 'connection(\''.$connection.'\')->';
        return "Schema::{$connection}drop('$tableName');";
    }
}
