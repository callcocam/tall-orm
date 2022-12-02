<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Illuminate\Support\Facades\Config;
use Tall\Orm\Http\Livewire\AbstractComponent;

use Tall\Schema\Schema;
use Illuminate\Support\Str;


abstract class ImportComponent extends AbstractComponent
{
    /**
     * @var $model
     * Carregado com o modelo do banco ex:(User, Post)
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    public $model;
    /**
     * @var $form_data
     * Para a atualizações das informações do formulario, mas quase nunca será necessário
     * exemplo de uso ( wire:model='form_data.name', wire:model.lazy='form_data.email', wire:model.defer='form_data.password')
     * Voce pode sobrescrever essas informações no component filho
     */
    public $form_data;


    /**
     * Controlar modal usando o livewire alpinejs etangle
     */
    public $showModal = false;

    
    public array $fileHeaders = [];

    public array $columnMaps = [];

    public array $requiredColumnMaps = [];


     /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de um novo cadastro do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshImport($data=[]){/** Ações aqui */}
     
     /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function active()
    {
        if ($this->model->exists) {
            if ($columnName = data_get($this->form_data, $this->columnName, false)) {
                return sprintf('Importar %s', $columnName);
            }
        }
        return __("Importar registros");
    }

    /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function title()
    {
       
        return __(config('app.name'));
    }

    
    /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function description()
    {
        if($this->config){
            return $this->config->name;
        }
        return class_basename($this->model);
    }

    /**
     * Carrega os valores iniciais do component no carrgamento do messmo
     * O resulta final será algo do tipo form_data.name='Informação vinda do banco'
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function setFormProperties($model = null, $currentRouteName=null)
    {

        $this->model = $model;

        if ($model) {
            $this->form_data = $model->toArray();
        }
    }


    
    public function setColumnsProperties()
    {
            $table = app($this->model->model)->getTable();

            Config::set('database.default', app($this->model->model)->getConnectionName());

            $columns = $this->makeSchema()->getTable($table)->getColumns()->toArray();

            $requiredColumnMaps = collect($this->generateForeignKeys($table, $columns))->filter(function($column){
                if(is_array($column)){
                    return false;
                }
                
                if(in_array($column->getName(), ['updated_at'])){
                    return false;
                }
                return $column->isNotNull();
            })->map(function($column){
                return $column->getName();
            })->toArray();

            if($requiredColumnMaps){
                $this->requiredColumnMaps = array_combine($requiredColumnMaps, $requiredColumnMaps);
            }
            $columns = collect($this->generateForeignKeys($table, $columns))
            ->filter(function($column){
                if(is_array($column)){
                    return false;
                }
                if(in_array($column->getName(), ['updated_at'])){
                    return false;
                }
                return true;
            })
            
            ->map(function($column){
                return $column->getName();
            })->toArray();

            $this->columnMaps = collect($columns)->mapWithKeys(fn($column)=>[$column=>''])->toArray();
        
    }
    /**
     * Generates foreign key migrations.
     *
    * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function generateForeignKeys($table, $columns): array
    {
  
        $foreignKeys = $this->makeSchema()->getTableForeignKeys($table);     
        if ($foreignKeys->isNotEmpty()) {
            if ($foreignKeys) {
                foreach($foreignKeys as $foreignKey){
                    $name = $foreignKey->getForeignTableName();
                    $method = Str::singular($name);
                    $columns[$method] = $this->makeSchema()->getTable($name)->getColumns()->toArray();
                }
            }
        }
        return $columns;
    }
     /**
     * Get DB schema by the database connection name.
     *
    * @throws \Exception
     */
    protected function makeSchema()
    {
        return Schema::make();
    }


}
