<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/

namespace Tall\Orm\Core\Migrations;

class FieldGenerator
{
    use MigrationIndex;

    protected $make_field;
    protected $table;
    protected $tableName;

    public function __construct($make_field, $table, $tableName)
    {
        $this->make_field = $make_field;
        $this->table = $table;
        $this->tableName = $tableName;
    }
    
    public function INT(){
        if(data_get($this->make_field,'column_primary'))
        {
            return $this->table->id($this->getColumnName());
        }
        
        if(data_get($this->make_field,'column_index')){
            $indexName = sprintf("%s_%s_index", $this->tableName, $this->getColumnName());
            $this->_dropIndexIfExist($this->tableName, $indexName);
            return $this->table->integer($this->getColumnName())
            ->nullable($this->getColumnNullable())->index($indexName);
        }
        return $this->table->integer($this->getColumnName())->nullable($this->getColumnNullable());
    }
    
    public function BIGINT(){
        if(data_get($this->make_field,'column_primary'))
        {
            return $this->table->id($this->getColumnName());
        }
        
        if(data_get($this->make_field,'column_index')){
            $indexName = sprintf("%s_%s_index", $this->tableName, $this->getColumnName());
            $this->_dropIndexIfExist($this->tableName, $indexName);
            return $this->table->unsignedBigInteger($this->getColumnName())
            ->nullable($this->getColumnNullable())->index($indexName);
        }
        return $this->table->unsignedBigInteger($this->getColumnName())->nullable($this->getColumnNullable());
    }

    protected function CHAR(){
        if(data_get($this->make_field,'column_primary'))
        {
            return $this->table->uuid($this->getColumnName())->primary();
        }
        
        if(data_get($this->make_field,'column_index')){
            return $this->table->uuid($this->getColumnName())->nullable($this->getColumnNullable())->index();
        }
       
        return $this->table->uuid($this->getColumnName())->nullable($this->getColumnNullable());
    }

    protected function TEXT(){
        return $this->table->text($this->getColumnName())->nullable($this->getColumnNullable());
    }

    protected function DATETIME(){
        return $this->table->dateTime($this->getColumnName())->nullable($this->getColumnNullable());
    }

    protected function TIMESTAMP(){
        return $this->table->timestamp($this->getColumnName())->nullable($this->getColumnNullable());
    }

    protected function VARCHAR(){
        if(data_get($this->make_field,'column_index')){
            return $this->table->string($this->getColumnName())->nullable($this->getColumnNullable())->index();
        }
        
        if(data_get($this->make_field,'column_unique')){
            return $this->table->string($this->getColumnName())->nullable($this->getColumnNullable())->unique();
        }
        return $this->table->string($this->getColumnName(),data_get($this->make_field,'column_with',255))->nullable($this->getColumnNullable());
    }


    
    protected function getColumnName()
    {
        return data_get($this->make_field,'column_name');
    }

    protected function getColumnType()
    {
        return data_get($this->make_field,'column_type');
    }

    protected function getColumnNullable()
    {
        return data_get($this->make_field,'column_nullable');
    }
}
