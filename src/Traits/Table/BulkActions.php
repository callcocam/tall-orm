<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Traits\Table;

/**
 * Trait BulkActions.
 */
trait BulkActions
{

    public $selectPage = false;
    public $selectAll = false;
    public $selected = [];

    public function renderingWithBulkActions()
    {
        if ($this->selectAll) $this->selectPageRows();
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
        $this->selectPage = false;
        $this->isShowPopper = false;
    }

    public function updatedSelectPage($value)
    {
        if ($value) return $this->selectPageRows();

        $this->selectAll = false;
        $this->isShowPopper = false;
        $this->selected = [];
    }

    public function selectPageRows()
    {
        $this->selected = $this->models()->pluck('id')->map(fn($id) => (string) $id);
    }

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function getSelectedRowsQueryProperty()
    {
        return (clone $this->models())
            ->unless($this->selectAll, fn($query) => $query->whereKey($this->selected));
    }


    public function exportSelected()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, 'transactions.csv');
    }

    public function deleteSelected()
    {
        $deleteCount = $this->selectedRowsQuery->count();

        $this->selectedRowsQuery->delete();
        
        $this->isShowPopper = false;

        /**
         * Informação para o PHP session
         */
        session()->flash('notification', ['text' => sprintf("%s registro(s) apagado(s) com sucesso!" , $deleteCount), 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
        /**
         * Informação em forma de evento para o java script
         */
        $this->dispatchBrowserEvent('notification', ['text' => sprintf("%s registro(s) apagado(s) com sucesso!" , $deleteCount), 'variant'=>'success', 'time'=>3000, 'position'=>'right-top']);
        /**
         * Atualizar informações em components interlidados
         */
        $this->emit('refreshDelete', [ 
            'result'=>true
        ]);
    }
    
}
