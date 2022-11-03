<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Traits;

trait Kill
{
    protected $confirming;

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }
    public function close()
    {

        $this->confirming = null;
    }

    public function kill($id, $callback=null)
    {
        try {
            $this->query()->find($id)->delete();
            if($callback){
                call_user_func_array($callback,[ 
                    'result'=>true
                ]);
            }else{
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
            }
            $this->confirming = null;
            return ;
        }catch (\PDOException $PDOException){
            $this->confirming = null;
            $this->PDOException($PDOException);
        }
    }
    
    public function delete($callback=null)
    {
        try {
            $this->model->delete();
            if($callback){
                call_user_func_array($callback,[ 
                    'result'=>true
                ]);
            }else{
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
            }
            $this->confirming = null;
            return ;
        }catch (\PDOException $PDOException){
            $this->confirming = null;
            $this->PDOException($PDOException);
        }
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
}
