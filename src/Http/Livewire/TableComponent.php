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
use Illuminate\Support\{Str ,Arr};
use Carbon\Carbon as CarbonCarbon;
use Tall\Cms\Http\Livewire\AbstractComponent;
use Tall\Orm\Traits\Table\Pagination;
use Tall\Orm\Traits\Table\Search;
use Tall\Orm\Traits\Table\Sorting;

abstract class TableComponent extends AbstractComponent
{
    use AuthorizesRequests, WithPagination, Search, Sorting, Pagination;

    /**
     * @var $filters
     * Coleção de filtros para listar e condiçoes de vizualização, que serão passados para a url
     */
    public $filters = [];
    public $status = [];
    public $tableField;
    
    protected $queryString = [
        'filters' => ['except' => []],
        'page' => ['except' => 1],
    ];

    protected $paginationTheme = 'card';

    public function setUp($currentRoute=null)
    {
       if($currentRoute){
            session()->put('back', $currentRoute);
       }

       if($query = $this->query()){
            $this->status = $query->whereStatus('published')->pluck('id','id')->toArray();

        //    $this->tableField =  app('make')->firstOrCreate([
        //         'name'=>$query->getModel()->getTable()
        //     ]);
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
        if($query = $this->query()){
            return class_basename($query->getModel());
        }
    }
    /**
     * Rota para cadastra um novo registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_create()
    {
        return null;
    }
    /**
     * Rota para editar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_edit()
    {
        return null;
    }
    /**
     * Rota para visualizar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_show()
    {
        return null;
    }
    /**
     * Rota para deletar um registro
     * Voce deve sobrescrever essas informações no component filho (opcional)
     */
    protected function route_delete()
    {
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
            'create'=>$this->route_create(),
            'edit'=>$this->route_edit(),
            'show'=>$this->route_show(),
            'delete'=>$this->route_delete(),
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
            'models'=>$this->models(),
            'columns'=>$this->columns(),
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
            if ($range = data_get($this->filters ,'range')){
                $start = Str::beforeLast($range, ' ');
                $end = Str::afterLast($range, ' ');
                $builder->whereBetween($this->data_field, [CarbonCarbon::parse($start)->format('Y-m-d'), CarbonCarbon::parse($end)->format('Y-m-d')]);                   
            }
    
            if ($status = data_get($this->filters ,'status')){
                $builder->whereIn('status', [$status]);                   
            }
            return $this->appendGuery($builder)->orderBy($this->getSortField(), $this->direction)->paginate(data_get($this->filters ,'perPage'));

        }
        return null;   
       
    }

    public function appendGuery($builder)
    {
        return $builder;
    }

    public function updatedFilters($data)
    {
       foreach($this->filters as $key => $value){
            if(empty($value)){
                unset($this->filters[$key]);
            }
       }
    }
}
