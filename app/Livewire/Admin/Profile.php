<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Title('Profil Pengguna')]
class Profile extends Component
{

    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $foto_profil = null;

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
    }


    public function updateProfile()
    {
        $user = Auth::user();

        $validate = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'foto_profil' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name' => $validate['name'],
            'email' => $validate['email'],
        ];

        if ($this->foto_profil) {
            if (!empty($user->foto_profil)) {
                $oldPath = storage_path('app/public' . $user->foto_profil);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $data['foto_profil'] = $this->foto_profil->store('foto-profil', 'public');
        }

        $user->update($data);
        $this->reset('foto_profil');
        session()->flash('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        session()->flash('success_password', 'Password berhasil diperbarui');
    }



    public function render()
    {
        return view('livewire.admin.profile', [
            'user' => Auth::user(),
        ]);
    }
}
