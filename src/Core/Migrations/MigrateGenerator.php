<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/

namespace Tall\Orm\Core\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Str;
use ReflectionMethod;
use Tall\Orm\Core\Migrations\FieldGenerator;

/**
 * Migrate generator
 */
trait MigrateGenerator
{
    
    public function up()
    {
        if( $make_fields = $this->model->make_fields){
            $tableName = $this->model->table_name;
            if(Schema::hasTable($tableName)){
                $this->table_update($make_fields, $tableName);
            }
            else{
                $this->table_create($make_fields, $tableName);                
            }
            $this->generateForeignKey($this->model->make_field_fks()
                ->whereNotNull('local_key_id')
                ->whereNotNull('make_model_id')->get(), $tableName);
        }

    }

    public function down($name)
    {
        # code...
    }

    protected function table_create($make_fields ,$name)
    {
        
            Schema::create($name, function (Blueprint $table) use($make_fields, $name){
                foreach($make_fields as $make_field){
                   
                    $this->table_data($table, $make_field, $name);
                }
            });
    }

    protected function table_update($make_fields, $name)
    {
        if( $make_fields){
            Schema::table($name, function (Blueprint $table) use($make_fields,$name){
                foreach($make_fields as $make_field){
                    if(Schema::hasColumn($name, data_get($make_field, 'column_name'))){
                        $this->table_data($table, $make_field, $name)->change();
                    }
                    else{
                        $this->table_data($table, $make_field, $name);
                    }
                    
                }
                
            });
    
           }
    }

    protected function table_data($table, $make_field, $name)
    {
        
        $object = new FieldGenerator($make_field,$table,$name);
        $reflectionMethod = new ReflectionMethod($object, data_get($make_field, 'column_type'));
        return $reflectionMethod->invokeArgs($object, array($make_field,$table,$name));
    
    }

    protected function generateForeignKey($make_field_fks, $tableName)
    {
        if( $make_field_fks){           
            foreach ($make_field_fks as $make_field_fk) {
                Schema::table($tableName, function (Blueprint $table) use($make_field_fk,$tableName){
                     if($make_field_local = $make_field_fk->make_field_local){
                        //Chave primaria da tabela relacionada ex:id 
                        $tableForeignKeyColumnName = $make_field_fk->make_field_foreign->column_name;
                        //Chave estrangeira da tabela principal ex:user_id 
                        $tableLocalKeyColumnName = $make_field_local->column_name;
                        //Nome da tabela do relacionamento ex:users
                        $tableForeignKeyName = $make_field_fk->make_model->table_name;
                        //criar um nome para a chave estrangeira
                        $foreignKeyName = substr(sprintf("%s_%s_foreign",$tableForeignKeyName, $make_field_local->column_name),0 ,50);
                        $this->_dropForeignKeyIfExist($tableName, $foreignKeyName);
                        $table->foreign($tableLocalKeyColumnName)->references($tableForeignKeyColumnName)->on($tableForeignKeyName)->cascadeOnDelete();
                        // $table->foreignUuid($make_field_local->column_name)->nullable()->constrained($tableForeignKeyName)->cascadeOnDelete(); 
                     } 
                });
            }
        }
    }

    protected function _dropForeignKeyIfExist($tableName, $indexName)
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName, $indexName) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->introspectTable($tableName);
            if ($doctrineTable->hasForeignKey($indexName)) {
                $table->dropForeignIdFor($indexName);
            }
        });
    }
}
