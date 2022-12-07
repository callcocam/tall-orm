<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\{Str };
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Support\Facades\Route;
use Tall\Orm\Traits\Table\BulkActions;
use Tall\Orm\Traits\Table\CachedRows;
use Tall\Orm\Traits\Table\Pagination;
use Tall\Orm\Traits\Table\Search;
use Tall\Orm\Traits\Table\Sorting;


abstract class TableComponent extends AbstractComponent
{
    use AuthorizesRequests, WithPagination, Search, Sorting, BulkActions, CachedRows, Pagination;

    /**
     * @var $filters
     * Coleção de filtros para listar e condiçoes de vizualização, que serão passados para a url
     */
    public $filters = [];
    public $status = [];
    public $params = [];
    public $perPage = 12;
    public $path;
    public $tableField;
    public $data_field = "created_at";
    
     /**
     * Indicates if user deletion is being confirmed.
     *
     * @var bool
     */
    public $isFilterExpanded = false;
    public $showDeleteModal = false;
    public $isShowPopper = false;

    /**
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmisFilterExpanded()
    {

        $this->isFilterExpanded = !$this->isFilterExpanded;
    }

    protected $queryString = [
        'filters' => ['except' => []],
        'page' => ['except' => 1],
    ];

    protected $paginationTheme = 'card';

    public function setUp($currentRouteName=null, $moke = true)
    {
       $this->currentRouteName = $currentRouteName;
     
       $this->authorize($this->permission);
       
      if($moke) $this->setConfigProperties($this->moke($this->getName()));
       
       if($currentRouteName){
            session()->put('back', $currentRouteName);
       }

       if($query = $this->query()){
           // $this->status = $query->whereStatus('published')->pluck('id','id')->toArray();
       }

    }
    /**
     * Fução para iniciar o carregamento da model
     * Voce deve sobrescrever essas informações no component filho 
     * ex: return Post::query()
     * ex: return Post::query()->orderBy('name')
     * ex: return Post::query()->orderBy('name')->whre('status','published')
     */
    abstract protected function query();    

    /**
     * Parametros (array) de informações
     * Usado para atualizar as informações do component depois de uma exclusão do registro
     * Voce pode sobrescrever essas informações no component filho
     */
    public function refreshDelete($data=[]){
       
        $this->resetPage();
    }

    /**
     * Monta automaticamente o titulo da pagina
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function title()
    {
        return config('app.name');
    }
    /**
     * Monta automaticamente o subtitulo da pagina
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function description()
    {
        if($this->config){
            return $this->config->name;
        }
        if($query = $this->query()){
            return class_basename($query->getModel());
        }
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
        if($query = $this->query()){
            return get_class( $query->getModel());
        }
    }

    /**
     * Rota para cadastra um novo registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_create()
    {
        if($this->config){
            $create = sprintf("%s.create",$this->config->route);
            if(Route::has($create )){
                $params=[];
                // if($url = $this->config->url){
                //     $params[Str::lower($this->config->model)] = $url;
                // }
                return route($create , $params);
            }
        }
        return null;
    }
    /**
     * Rota para editar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_edit()
    {
        if($this->config){
            return sprintf("%s.eidt",$this->config->route);
         }
        return null;
    }
    /**
     * Rota para visualizar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_show()
    {
        if($this->config){
            return sprintf("%s.view",$this->config->route);
         }
        return null;
    }
    /**
     * Rota para deletar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_delete()
    {
        if($this->config){
            return sprintf("%s.delete",$this->config->route);
         }
        return null;
    }

    /**
     * Função para trazer uma lista de colunas (opcional)
     * Geralmente usada com um component de table dinamicas
     * Voce pode sobrescrever essas informações no component filho 
     */
    public function columns(){
        return [];
    }
    
    /**
     * tableAttr
     * Informação basica da visualização
     * Nome da visualização
     * Uma descrição com detalhes da visualização
     * create rota para cadastrar um nono registro
     * edit rota para atualizar um registro
     * show rota para visualizar um registro
     * detete rota para deletar um registro
     * Uma rota de retorno para a lista ou para outra visualização pré definida
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function tableAttr(): array
    {
      
        return [
            'title'=>$this->title(),
            'description'=>$this->description(),
            'route'=>route('admin'),
            'crud'=>[                
                'create'=>$this->route_create(),
                'edit'=>$this->route_edit(),
                'show'=>$this->route_show(),
                'delete'=>$this->route_delete(),
            ]
        ];
    }
    /**
     * Data
     * Informação que serão passsadas para view template
     * Coloque todos dado que prentende passar para a view
     * Voce pode sobrescrever essas informações no component filho
     */
    protected function data(){
        return [
            'tableAttr'=>$this->tableAttr(),
            'models'=>$this->cache(function () {
                return $this->applyPagination($this->models());
            }),
            'columns'=>$this->columns(),
            'statusOptions'=>array_combine(['draft','published'],['draft','published']),
        ];
    }
    
    /**
     * Função final que faz a consulta no banco de dados
     * Voce pode sobrescrever essas informações no component filho
     * @return Builder
     */
    public function models()
    {
        if ( $builder = $this->query()) {
            
            $this->useCachedRows();

            $builder->where(function (Builder $builder) {
                /**
                 * Usa as colunas para filtral
                 * Temos um exemplo simples nesse pacote de um classe para montar as colunas
                 * Obrigatoriamente essa classe deve conter uma property $name 
                 * um campos $searchable e um $sortable que pode ser usada na visualização para mostrar ou esconder um botão para ordenar a coluna
                 */
                foreach ($this->columns() as $column) {
                    if ($column->searchable) {
                        if (Str::contains($column->name, '.')) {
                            $relationship = $this->relationship($column->name);

                            $builder->orWhereHas($relationship->name, function (Builder $query) use ($relationship) {
                                $query->where($relationship->name, 'like', '%' . data_get($this->filters, 'search') . '%');
                            });
                        } elseif (Str::endsWith($column->name, '_count')) {
                            // No clean way of using having() with pagination aggregation, do not search counts for now.
                            // If you read this and have a good solution, feel free to submit a PR :P
                        } else {
                            $builder->orWhere($builder->getModel()->getTable() . '.' . $column->name, 'like', '%' . trim(data_get($this->filters, 'search')) . '%');
                        }


                    }
                }
            });

            $builder->when(data_get($this->filters,'status'), fn($query, $status) => $query->where('status', $status));
          
            $builder->when(data_get($this->filters,'start'), fn($query, $date) => $query->where($this->data_field, '>=', CarbonCarbon::parse($date)));
           
            $builder->when(data_get($this->filters,'end'), fn($query, $date) => $query->where($this->data_field, '<=', CarbonCarbon::parse($date)));
                       
            return $this->appendGuery($builder)->orderBy($this->getSortField(), $this->getDirection());

        }
        return null;   
       
    }

    public function appendGuery($builder)
    {
        return $builder;
    }

    public function clearFilters()
    {
       $this->reset(['filters']);
       
       $this->confirmisFilterExpanded();
    }

    public function updatedFilters($data)
    {
        $this->resetPage();

       foreach($this->filters as $key => $value){
            if(empty($value)){
                unset($this->filters[$key]);
            }
       }
    }

    public function getImportProperty()
    {
        return 'tall::admin.imports.csv-component';
    }
}
