<?php
namespace App\Livewire\Player;

use Livewire\Component;

class AvatarPicker extends Component
{
    public string $selected = '';

    public function mount(): void
    {
        $this->selected = auth()->user()->avatar ?? 'astro';
    }

    public function save(): void
    {
        auth()->user()->update(['avatar' => $this->selected]);
        $this->dispatch('avatar-updated');
    }

    public function render()
    {
        $avatars = config('adventure.avatars');
        return view('livewire.player.avatar-picker', compact('avatars'));
    }
}
