<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Core\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait MigrationIndex
{
    public function _dropIndexIfExist($tableName, $indexName)
    {
        if(Schema::hasTable($tableName)){
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $indexName) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->introspectTable($tableName);
                if ($doctrineTable->hasIndex($indexName)) {
                    $table->dropIndex($indexName);
                }
            });
        }
    }

}