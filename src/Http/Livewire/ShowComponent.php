<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Tall\Orm\Http\Livewire\AbstractComponent;

abstract class ShowComponent extends AbstractComponent
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
     * Nome da coluna geralmente de um modelo de tabela
     * que serve com padrão para busca e informações visuais para o usuário
     * Voce pode sobrescrever essas informações no component filho
     */
    public $columnName = 'name';

    /**
     * Controlar modal usando o livewire alpinejs etangle
     */
    public $showModal = false;

    /**
     * Nome da coluna geralmente de um modelo de tabela
     * que serve com padrão para busca e informações visuais para o usuário
     * Voce pode sobrescrever essas informações no component filho
     */
    public $columnDescription = 'subtitle';

    /**
     * @var string[]
     */
    protected $listeners = ['refreshDelete', 'refreshUpdate', 'refreshCreate', 'refreshShow'];

     /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de um novo cadastro do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshCreate($data=[]){/** Ações aqui */}

    /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de uma atualização do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshUpdate($data=[]){/** Ações aqui */}

    /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de uma exclusão do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshDelete($data=[]){/** Ações aqui */}

    /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de uma exclusão do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshShow($data=[]){/** Ações aqui */}

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
        return class_basename($this->model);
    }
        
    /**
     * Monta automaticamente o nome da model
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function modelClass()
    {
        if($this->config){
            return $this->config->model;
        }
        return get_class($this->model);
    }

    /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function active()
    {
        if ($columnName = data_get($this->form_data, $this->columnName, false)) {
            return sprintf('Visualisar %s', $columnName);
        }
    }
    /**
     * showAttr
     * Informação basica da visualização
     * Nome da visualização
     * Uma descrição com detalhes da visualização
     * Uma rota de retorno para a lista ou para outra visualização pré definida
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function showAttr(): array
    {
      
        return [
            'title'=>$this->title(),
            'description'=>$this->description(),
            'active'=>$this->active(),
            'route'=>session()->get('back'),
        ];
    }
    /**
     * Data
     * Informação que serão passsadas para view template
     * Coloque todos dado que prentende passar para a view
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function data(){

        $fields['fields']= $this->fields();
        $fields['showAttr']= $this->showAttr();
        return $fields;

    }
  
    /**
     * Monta um array de campos (opcional)
     * Voce pode sobrescrever essas informações no component filho
     * Uma opção e trazer essas informações do banco
     * @return array
     */
    protected function fields()
    {
        return [];
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
        /**
         * Esse trecho de código garante que campos que não vem do banco também sejam pré carregados
         * ele não substitui as informações vindas do bano de dados
         */
        foreach ($this->fields() as $field):
            if (!isset($this->form_data[$field->name])):
                $array = in_array($field->type, ['checkbox', 'file']);
                if (in_array($field->type, ['file'])) {
                    if ($this->form_data[$field->name] = data_get($model, $field->name)) {
                        /**
                         * O alias é um apelido para um campo de imagem ou file perssonalizado na model
                         */
                        $this->form_data[$field->alias] = data_get($model, $field->alias);
                        /**
                         * o campo ou method file geralmente e usada para arquivos
                         */
                        if(method_exists($model, 'file')){
                            $this->form_data[$field->name] = data_get($model, $field->name)->file;
                        }
                        /**
                         * o method cover geralmente é usada pa imagens e fotos
                         */
                        if(method_exists($model, 'cover')){
                            $this->form_data[$field->name] = data_get($model, $field->name)->cover;
                        }
                    }
                } else {
                    $this->form_data[$field->name] = $field->default ?? ($array ? [] : null);
                }
            endif;
        endforeach;
    }

}
