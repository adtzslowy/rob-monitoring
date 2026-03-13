<?php

namespace App\Livewire\Admin;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $roleName = null;
    public ?string $storedFotoProfil = null;

    public string $qrCode = '';
    public string $qrPayload = '';

    public $foto_profil = null;

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user()->loadMissing('roles:id,name');

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->storedFotoProfil = $user->foto_profil;
        $this->roleName = $user->roles->first()?->name ?? 'No Role';

        $this->generateQrCode($user);
    }

    private function generateQrCode($user): void
    {
        $renderer = new ImageRenderer(
            new RendererStyle(180),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $payload = "ID: {$user->id}\n"
            . "Nama: {$user->name}\n"
            . "Email: {$user->email}\n"
            . "Role: {$this->roleName}";

        $this->qrPayload = $payload;
        $this->qrCode = $writer->writeString($payload);
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($this->foto_profil) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $data['foto_profil'] = $this->foto_profil->store('foto-profil', 'public');
        }

        $user->update($data);

        $fresh = $user->fresh()->loadMissing('roles:id,name');

        $this->storedFotoProfil = $fresh->foto_profil;
        $this->name = (string) $fresh->name;
        $this->email = (string) $fresh->email;
        $this->roleName = $fresh->roles->first()?->name ?? 'No Role';

        $this->generateQrCode($fresh);

        $this->reset('foto_profil');

        session()->flash('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        session()->flash('success_password', 'Password berhasil diperbarui.');
    }

    public function render(): View
    {
        return view('livewire.admin.profile');
    }
}