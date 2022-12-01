<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Route;
use Tall\Orm\Http\Livewire\AbstractComponent;

abstract class DeleteComponent extends AbstractComponent
{
    use AuthorizesRequests;
    /**
     * @var $model
     * Carregado com o modelo do banco ex:(User, Post)
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    public $model;
   
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
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function title()
    {
        if ($this->model->exists) {
            if ($columnName = data_get($this->model, $this->columnName, false)) {
                return sprintf('Excluir %s', $columnName);
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
            if ($columnDescription = data_get($this->model, $this->columnDescription, false)) {
                return $columnDescription;
            }
        }
        return null;
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
        ];
    }
    /**
     * Data
     * Informação que serão passsadas para view template
     * Coloque todos dado que prentende passar para a view
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function data(){

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
        $this->authorize($this->permission);

        $this->model = $model;

        if ($model) {
            $this->form_data = $model->toArray();
        }

        $this->showModal = true;

        $this->setUp(  Route::currentRouteName() );
        
        $this->setConfigProperties(  $this->moke( $this->getName() ) );

    }

    public function delete()
    {
        try {
            $this->model->delete();
            /**
             * Informação para o PHP session
             */
            session()->flash('notification', ['text' => "Registro apagado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
            /**
             * Informação em forma de evento para o java script
             */
            $this->dispatchBrowserEvent('notification', ['text' => "Registro apagado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);

            
             if(Route::has($this->list)){
                 return redirect()->route($this->list);
             }

            return true;
        }catch (\PDOException $PDOException){
            $this->PDOException($PDOException);
        }
    }
    
    public function cancel()
    {
       
        $this->showModal = false;

        return redirect()->route($this->list);

    }

    public function getListProperty()
    {
        if($this->config){
            return $this->config->route;
         }
        return null;
    }

     /**
     * Envia uma menssagem de erro para o usuário
     * você deve tratar essa informação na sua visualização
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function PDOException($PDOException){
        /**
         * Informação em forma de evento para o java script
         */
        $this->dispatchBrowserEvent('notification', ['text' => $PDOException->getMessage(), 'variant'=>'error', 'time'=>3000, 'position'=>'right-top']);
    }

    public function view($compnent="-component")
    {
        return 'tall::delete';
    }
}
