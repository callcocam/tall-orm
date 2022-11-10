<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Traits\Table;

/**
 * Trait Pagination.
 */
trait Pagination
{
    /**
     * Displays per page and pagination links.
     *
     * @var bool
     */
    public $paginationEnabled = true;

    /**
     * The options to limit the amount of results per page.
     *
     * @var array
     */
    public function getPerPageOptionsProperty(){
        return config('tall-orm.table.items-per-page',[12,24,48,100,200]);
    }

    /**
     * https://laravel-livewire.com/docs/pagination
     * Resetting Pagination After Filtering Data.
     */
    public function updatingFiltersSearch()
    {
        $this->resetPage();
    }

    /**
     * https://laravel-livewire.com/docs/pagination
     * Resetting Pagination After Changing the perPage.
     */
    public function updatingFiltersPerPage()
    {
        $this->resetPage();
    }

    public function applyPagination($query)
    {
        if($query)
            return $query->paginate(data_get($this->filters ,'perPage', $this->perPage));
    }

}
