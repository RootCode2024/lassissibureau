<x-app-layout>
    <x-slot name="header">
        Modifier l'utilisateur : {{ $user->name }}
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        
        {{-- Modifier infos --}}
        <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Informations générales</h3>
            </div>

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Modifier Rôle --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestion du rôle</h3>
            <form method="POST" action="{{ route('users.role', $user) }}" class="flex items-end gap-4">
                @csrf
                @method('PATCH')
                
                <div class="flex-grow">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle actuel</label>
                    <select name="role" id="role" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption['value'] }}" {{ $user->hasRole($roleOption['value']) ? 'selected' : '' }}>{{ $roleOption['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                    Mettre à jour le rôle
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
