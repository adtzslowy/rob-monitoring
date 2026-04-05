<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact as ModelsContact;
use App\ContactStatus;

class Contact extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:5',
    ];

    public function submit()
    {
        $this->validate();

        ModelsContact::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'status' => ContactStatus::NEW,
        ]);

        $this->reset();

        $this->dispatch('notify', message: 'Pesan berhasil dikirim 🚀');
    }

    public function render()
    {
        return view('livewire.contact');
    }
}