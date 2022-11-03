<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Http\Livewire\Admin\Make;

use Tall\Orm\Http\Livewire\FormComponent;
use Tall\Orm\Models\Make;

class CreateComponent extends FormComponent
{

    public function mount(Make $model)
    {
        $this->setFormProperties($model);
        
    }

    protected function view($component= "-component")
    {
        return 'tall::admin.make.create-component';
    }
}
