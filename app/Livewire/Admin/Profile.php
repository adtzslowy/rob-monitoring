<?php

namespace App\Livewire\Admin;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

        $payload = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $this->roleName,
        ], JSON_UNESCAPED_UNICODE);

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
            'foto_profil' => ['nullable', 'image', 'max:1024'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($this->foto_profil) {
            if (!empty($user->foto_profil)) {
                $oldPath = storage_path('app/public/' . $user->foto_profil);

                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
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
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
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

        session()->flash('success_password', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}