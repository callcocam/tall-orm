<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Tall\Orm\Http\Livewire\AbstractComponent;

abstract class DeleteComponent extends AbstractComponent
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
     * Nome da coluna geralmente de um modelo de tabela
     * que serve com padrão para busca e informações visuais para o usuário
     * Voce pode sobrescrever essas informações no component filho
     */
    public $columnDescription = 'subtitle';

    /**
     * @var string[]
     */
    protected $listeners = ['refreshDelete', 'refreshUpdate', 'refreshCreate'];

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
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function title()
    {
        if ($this->model->exists) {
            if ($columnName = data_get($this->form_data, $this->columnName, false)) {
                return sprintf('Visualisar %s', $columnName);
            }
        }
    }

    /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function description()
    {
        if ($this->model->exists) {
            if ($columnDescription = data_get($this->form_data, $this->columnDescription, false)) {
                return $columnDescription;
            }
        }
        return null;
    }

    /**
     * deleteAttr
     * Informação basica da visualização
     * Nome da visualização
     * Uma descrição com detalhes da visualização
     * Uma rota de retorno para a lista ou para outra visualização pré definida
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function deleteAttr(): array
    {
      
        return [
            'title'=>$this->title(),
            'description'=>$this->description(),
            'route'=>$this->back(session()->get('back')),
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
        $fields['deleteAttr']= $this->deleteAttr();
        return $fields;

    }
  

    /**
     * Carrega os valores iniciais do component no carrgamento do messmo
     * O resulta final será algo do tipo form_data.name='Informação vinda do banco'
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function setFormProperties($model = null, $currentRouteName=null)
    {

        $this->model = $model;

    }

}
