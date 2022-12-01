<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Table\Traits;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/**
 * Class Column.
 */
trait TColumn
{
     /**
     * @var string
     */
    protected $component = "text";

     /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $label;
    
    /**
     * @var string
     */
    protected $icon;
    
    /**
     * @var string
     */
    protected $route;
   
    /**
     * @var bool
     */
    protected $selected = true;
    
    /**
     * @var bool
     */
    protected $visible = true;

    /**
     * @var bool
     */
    protected $searchable = true;
   
    /**
     * @var bool
     */
    protected $sortable = true;

    /**
     * Array com as a~ções 
     * ex: ação de editar excluir visualizar
     */
    protected $actions = [];

    /**
     * Array com as attributes do component 
     * ex: class='flex'
     */
    protected $attributes = [];

    /**
     * Condição para mostrar ou esconder um elemento
     */
    public function hiddenIf($condition)
    {
      $this->visible  = $condition;

      return $this;
    }

   public function route(string $route)
   {
      $this->route = $route;

      $permission = $this->user()->can($route);
      /**
       * Verificar se tem autorização para acesar a rota
       */
      if($permission){         
         /**
          * Verificar se a rota exxis
         */
         $this->hiddenIf(Route::has($route));
      }else{
         $this->hiddenIf($permission);
      }
        
      // $this->hiddenIf(Gate::allows($route));


      return $this;
   }


   /**
    * searchable blade
    */
   public function searchable($searchable)
   {
    $this->searchable = $searchable;

    return $this;
   }

   /**
    * component blade
    */
   public function component($component)
   {
    $this->component = $component;

    return $this;
   }

   /**
    * Icone svg
    */
   public function icon($icon)
   {
    $this->icon = $icon;

    return $this;
   }

   public function attributes()
   {
      return [];
   }


   public function permission(){
      $permission = request()->route()->getName();
      //    $permission = Str::afterLast($permission, '.');
      //    $permissions = Jetstream::$permissions;
      //    $permissions = array_combine($permissions,$permissions);
      //    return data_get($permissions, $permission, Jetstream::$defaultPermissions[0]);
      return $permission;
  }

  public function user(){

     return auth()->user();
  }

  public function team(){
     return $this->user()->currentTeam;
  }

    /**
     * @return string
     */
    public function __get($name)
    {
        return $this->{$name};
    }
}
