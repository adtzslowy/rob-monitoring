<?php

namespace App\Livewire\Admin;

use App\ContactStatus;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use Livewire\Attributes\Title;

#[Title('Kritik & Saran')]
class ContactAdmin extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $perPage = 10;

    public $modalOpen = false;
    public $selectedContact = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function refreshContacts()
    {
        // trigger re-render
    }

    public function openModal($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->status === ContactStatus::NEW) {
            $contact->update([
                'status' => ContactStatus::READ
            ]);
        }

        $this->selectedContact = $contact;
        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->selectedContact = null;
    }

    public function render()
    {
        $query = Contact::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('message', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.contact-admin', [
            'contacts' => $query->latest()->paginate($this->perPage),
        ]);
    }
}