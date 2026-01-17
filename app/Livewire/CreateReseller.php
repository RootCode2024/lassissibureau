<?php

namespace App\Livewire;

use App\Models\Reseller;
use Livewire\Component;

class CreateReseller extends Component
{
    public $name = '';

    public $phone = '';

    public $address = '';

    public $notes = '';

    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom est requis.',
        'phone.required' => 'Le téléphone est requis.',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $reseller = Reseller::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Revendeur créé avec succès.');

            return redirect()->route('resellers.show', $reseller);
        } catch (\Exception $e) {
            logger()->error('Erreur lors de la création du revendeur', [
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Erreur lors de la création : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.create-reseller');
    }
}
