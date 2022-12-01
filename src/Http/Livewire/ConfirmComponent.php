<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Tall\Orm\Http\Livewire\AbstractComponent;

abstract class ConfirmComponent extends AbstractComponent
{


    /**
     * @var $model
     * Carregado com o modelo do banco ex:(User, Post)
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    public $model;
    
    /**
     * Carrega os valores iniciais do component no carrgamento do messmo
     * O resulta final será algo do tipo form_data.name='Informação vinda do banco'
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function setFormProperties($model = null, $currentRouteName=null)
    {

        $this->model = $model;

    }

    protected function  view($sufix="-component"){
        return "tall::delete";
    }

    /**
     * Função basica do livewire, que carrega a vizualização na tela
     * Voce pode sobrescrever essas informações no component filho
     */
    public function render()
    {
        return view($this->view())
        ->with($this->data());
    }

    public function delete()
    {
        try {
            $this->model->delete();
            

            $this->showModal = false;

            /**
             * Informação para o PHP session
             */
            session()->flash('notification', ['text' => "Registro apagado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
            /**
             * Informação em forma de evento para o java script
             */
            $this->dispatchBrowserEvent('notification', ['text' => "Registro apagado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
            /**
             * Atualizar informações em components interlidados
             */
            $this->emit('refreshDelete', [ 
                'result'=>true
            ]);
            return true;
        }catch (\PDOException $PDOException){
            $this->PDOException($PDOException);
        }
    }
    public function cancel()
    {
       
        $this->showModal = false;

    }
}
