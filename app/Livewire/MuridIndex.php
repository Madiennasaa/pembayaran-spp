<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Murid;

class MuridIndex extends Component
{
    use WithPagination;

    // Gunakan tema Bootstrap agar pagination rapi
    protected $paginationTheme = 'bootstrap';

    // Variable untuk menampung input pencarian
    public $search = '';

    // Reset halaman ke 1 setiap kali user mengetik pencarian baru
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Murid::with('user')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.murid-index', [
            'murids' => $query->paginate(10)
        ]);
    }
}
