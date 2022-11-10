<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Livewire\Component;
 
abstract class AbstractComponent extends Component
{
    
    /**
     * @var $config
     * Carregado com o modelo de configuração dinamica
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    protected $config; 
    
    /**
     * Salva a rota corrente
     */
    public $currentRoute;
    
    /**
     * Controlar modal usando o livewire alpinejs etangle
     */
    public $showModal = false;
    
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
     * Essa função exige que você informe uma visualização para o component
     * Voce deve sobrescrever essas informações no component filho (obrigatório)
     */
    abstract protected function view($sufix="-component");
    
    
    /**
     * Carrega os valores iniciais do component no carrgamento do messmo
     * O resulta final será algo do tipo form_data.name='Informação vinda do banco'
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function setConfigProperties($config = null, $currentRouteName=null)
    {

        $this->config = $config;

    }

    /**
     * Define o layout para o component acessa via rota
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function layout(){

        return "tall::layouts.app";

    }
    /**
     * Permite passar informações para o layout
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function layoutData(){

        return [];

    }
    /**
     * Permite passar informações para a visualização
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function data(){

        return [];

    }
    /**
     * Função basica do livewire, que carrega a vizualização na tela
     * Voce pode sobrescrever essas informações no component filho
     */
    public function render()
    {
        return view($this->view())
        ->with($this->data())
        ->layout($this->layout(), $this->layoutData());
    }


     /**
     * Envia uma menssagem de erro para o usuário
     * você deve tratar essa informação na sua visualização
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function PDOException($PDOException){
        /**
         * Informação para o PHP session
         */
        session()->flash('notification', ['text' => $PDOException->getMessage(), 'variant'=>'error', 'time'=>3000, 'position'=>'right-top']);
        /**
         * Informação em forma de evento para o java script
         */
        $this->dispatchBrowserEvent('notification', ['text' => $PDOException->getMessage(), 'variant'=>'error', 'time'=>3000, 'position'=>'right-top']);
    }
    
    
    public function geGroupUpdatedOrder()
    {
       return [];
    }
    
    public function setGroupUpdatedOrder($data)
    {
        $orders = explode("|", $data);
        $orders = array_filter($orders);
        return $orders;
    }
}
