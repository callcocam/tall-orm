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
     * Essa função exige que você informe uma visualização para o component
     * Voce deve sobrescrever essas informações no component filho (obrigatório)
     */
    abstract protected function view($sufix="-component");
    
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
}
