<?php

namespace App\Livewire;

use App\Models\Reseller;
use Livewire\Component;

class EditReseller extends Component
{
    public Reseller $reseller;

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

    public function mount(Reseller $reseller)
    {
        $this->reseller = $reseller;
        $this->name = $reseller->name;
        $this->phone = $reseller->phone;
        $this->address = $reseller->address;
        $this->notes = $reseller->notes;
        $this->is_active = $reseller->is_active;
    }

    public function submit()
    {
        $this->validate();

        try {
            $this->reseller->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Revendeur mis à jour avec succès.');

            return redirect()->route('resellers.show', $this->reseller);
        } catch (\Exception $e) {
            logger()->error('Erreur lors de la mise à jour du revendeur', [
                'error' => $e->getMessage(),
                'reseller_id' => $this->reseller->id,
            ]);

            session()->flash('error', 'Erreur lors de la mise à jour : '.$e->getMessage());
        }
    }

    public function delete()
    {
        if ($this->reseller->hasPendingProducts()) {
            session()->flash('error', 'Impossible de supprimer ce revendeur car il a des produits en cours.');

            return;
        }

        try {
            $this->reseller->delete();
            session()->flash('success', 'Revendeur supprimé avec succès.');

            return redirect()->route('resellers.index');
        } catch (\Exception $e) {
            logger()->error('Erreur lors de la suppression du revendeur', [
                'error' => $e->getMessage(),
                'reseller_id' => $this->reseller->id,
            ]);

            session()->flash('error', 'Erreur lors de la suppression : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.edit-reseller');
    }
}
