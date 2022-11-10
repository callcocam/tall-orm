<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Illuminate\Http\UploadedFile;
use Tall\Orm\Http\Livewire\AbstractComponent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Livewire\WithFileUploads;
use Tall\Orm\Traits\Form\FollowsRules;
use Tall\Orm\Traits\Form\UploadsFiles;

abstract class FormComponent extends AbstractComponent
{
    use FollowsRules, WithFileUploads,UploadsFiles;
   
    /**
     * @var $config
     * Carregado com o modelo de configuração dinamica
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    public $path;

     /**
     * @var $model
     * Carregado com o modelo do banco ex:(User, Post)
     * Voce pode sobrescrever essas informações no component filho, mas quase nunca será necessário
     */
    public $model;

    /**
     * @var $form_data array
     * Para a atualizações das informações do formulario, mas quase nunca será necessário
     * exemplo de uso ( wire:model='form_data.name', wire:model.lazy='form_data.email', wire:model.defer='form_data.password')
     * Voce pode sobrescrever essas informações no component filho
     */
    public $form_data = [];

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
       
        return __(config('app.name'));
    }

      /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function active()
    {
        if ($this->model->exists) {
            if ($columnName = data_get($this->form_data, $this->columnName, false)) {
                return sprintf('Editar %s', $columnName);
            }
        }
        return __("Cadastrar novo registro");
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
     * formAttr
     * Informação basica da visualização
     * Nome da visualização
     * Uma descrição com detalhes da visualização
     * Uma rota de retorno para a lista ou para outra visualização pré definida
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function formAttr(): array
    {
   
        return [
            'title'=>$this->title(),
            'description'=>$this->description(),
            'routeList'=>session()->get('back'),
            'active'=>$this->active(),
            'span'=>$this->span(),
            'spanLeft'=>$this->spanLeft(),
            'spanRigth'=>$this->spanRigth(),
            'config'=>$this->config,
            'crud'=>[
                'list'=>$this->route_list($this->config),
                'edit'=>$this->route_edit($this->config),
            ]
        ];
    }

     /**
     * Rota para editar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_list($config = null)
    {
        if($config){
            if(Route::has($config->route)){
                $params =$this->path;
                return route($config->route, $params);
            }              
         }
        return null;
    }

     /**
     * Rota para editar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_edit($config = null)
    {
        // if($config){
        //     if($config){
        //         if ($this->model->exists) {
        //             if(Route::has(sprintf('%s.edit', $config->route))){
        //                 $params =$this->path;
        //                 $params['model'] =$this->model;
        //                 return route(sprintf('%s.edit', $config->route), $params);
        //             }
        //         }
        //      }
        //  }
        return null;
    }

    /**
     * Data
     * Informação que serão passsadas para view template
     * Coloque todos dado que prentende passar para a view
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function data(){

        $fields['fields']= $this->fields();
        $fields['formAttr']= $this->formAttr();
        return $fields;

    }
  
    public function span()
    {
        return '12';
    }
  
    public function spanLeft()
    {
        return '4';
    }
  
    public function spanRigth()
    {
        return '4';
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
        $this->currentRouteName = $currentRouteName;

        if ($model) {
            $this->form_data = $model->toArray();
        }
        /**
         * Esse trecho de código garante que campos que não vem do banco também sejam pré carregados
         * ele não substitui as informações vindas do bano de dados
         */
        foreach ($this->fields() as $field):
            if (!isset($this->form_data[$field->name])):
                $type = data_get($field->attributes, 'type');
                $name = $field->name;
                $array = in_array($type, ['checkbox', 'file']);
                if (in_array($type, ['file'])) {
                    if ($this->form_data[$name] = data_get($model, $name)) {
                        $alias = data_get($field, 'alias');
                        /**
                         * O alias é um apelido para um campo de imagem ou file perssonalizado na model
                         */
                        $this->form_data[$alias] = data_get($model, $alias);
                        /**
                         * o campo ou method file geralmente e usada para arquivos
                         */
                        if(method_exists($model, 'file')){
                            $this->form_data[$name] = data_get($model, $name)->file;
                        }
                        /**
                         * o method cover geralmente é usada pa imagens e fotos
                         */
                        if(method_exists($model, 'cover')){
                            $this->form_data[$name] = data_get($model, $name)->cover;
                        }
                    }
                } else {
                    $this->form_data[$name] = $field->default ?? ($array ? [] : null);
                }
            else:
                $field->setValue($this->form_data[$field->name]);
            endif;
        endforeach;
    }
    
    /**
     * pré tratamento e validações dos dados
     * Voce pode sobrescrever essas informações no component filho
     */
    public function submit()
    {
        if ($this->rules())
            $this->validate($this->rules());

        $field_names = [];
        foreach ($this->fields() as $field) $field_names[] = $field->name;
        $this->form_data = Arr::only($this->form_data, $field_names);
        /**
         * Verifica se existe campos do tipo file
         * e faz o upload dos arquivos casa exista files o imagens
         */
        if($this->uploadPhoto()){
            return $this->success();
        }
       return false;
    }

    
    protected function uploadPhoto()
    { 
     
            foreach($this->fileUpload() as $field_name => $uploaded_files){
              
                $this->fileUploadate( $field_name, $uploaded_files);
            }
       

        return true;
    }

    /**
     * @param $callback uma função anonima para dar um retorno perssonalizado
     * Função de sucesso ou seja passou por todas as validações e agora pode ser salva no banco
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function success($callback=null)
    {
        if ($this->model->exists) {
            /**
             * Atualiza uma informação existente
             */
            return $this->update($callback);
          
        } else {
            /**
             * Cadastra uma nova informação
             */
            return $this->create($callback);
        }
    }

    /**
     * Atualiza uma informação existente
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function update($callback=null){
        try {
            $this->model->update($this->form_data);
            if($callback){
                call_user_func_array($callback,[ 
                    'model'=>$this->model,
                    'result'=>true
                ]);
            }else{
                /**
                 * Informação para o PHP session
                 */
                session()->flash('notification', ['text' => "Registro atualizado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
                /**
                 * Informação em forma de evento para o java script
                 */
                $this->dispatchBrowserEvent('notification', ['text' => "Registro atualizado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
                /**
                 * Atualizar informações em components interlidados
                 */
                $this->emit('notification', ['text' => "Registro atualizado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
                $this->emit('refreshCreate', $this->model);
            }
            return true;
        } catch (\PDOException $PDOException) {
            $this->PDOException($PDOException);            
            return false;
        }
    }

    /**
     * Cadastra uma nova informação 
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function create($callback=null){
        try {
            $this->model = $this->model->create($this->form_data);
            if($callback){
                call_user_func_array($callback,[ 
                    'model'=>$this->model,
                    'result'=>true
                ]);
            }else{
                /**
                 * Informação para o PHP session
                 */
                session()->flash('notification', ['text' => "Registro criado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
                /**
                 * Informação em forma de evento para o java script
                 */
                $this->dispatchBrowserEvent('notification', ['text' => "Registro criado com sucesso!", 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
                /**
                 * Atualizar informações em components interlidados
                 */
                $this->emit('refreshCreate', $this->model);
            }
            return true;
        } catch (\PDOException $PDOException) {
            $this->PDOException($PDOException, $callback);
            return false;
        }
    }
  
     /**
     * Salvar e continuar com um novo cadastro ou continuar com a atualização
     * Voce pode sobrescrever essas informações no component filho
     */
    public function saveAndStay()
    {
     
        $this->submit();
    }

    /**
     * Salvar e ir para a visualização de edição
     * Voce pode sobrescrever essas informações no component filho
     */
    public function saveAndEditStay()
    {
        if ($this->submit())
            return $this->saveAndStayResponse();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAndStayResponse()
    {
        return $this->saveAndGoBackResponse();
    }

    /**
     * Salvar e voltar para a visualização dos registros (lista de registros)
     * Voce pode sobrescrever essas informações no component filho
     */
    public function saveAndGoBack()
    {
        if ($this->submit()) {
            return $this->saveAndGoBackResponse();
        }

    }

    /**
     *Salvar e voltar para a visualização dos registros (lista de registros)
     * Voce pode sobrescrever essas informações no component filho
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAndGoBackResponse()
    {

    }

}
