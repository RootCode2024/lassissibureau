<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-base sm:text-lg lg:text-xl text-gray-900 truncate">Modifier : {{ $user->name }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-4 sm:space-y-6">
        
        {{-- Modifier infos --}}
        <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 space-y-4 sm:space-y-6 shadow-sm">
            @csrf
            @method('PUT')
            
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Informations générales</h3>
                <p class="text-xs sm:text-sm text-gray-500">Modifiez les informations de base de l'utilisateur</p>
            </div>

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                    placeholder="Ex: Jean Dupont"
                >
                @error('name')
                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                    Email professionnel <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $user->email) }}" 
                    required 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                    placeholder="exemple@entreprise.com"
                >
                @error('email')
                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2 sm:gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 active:bg-gray-950 transition-all hover:shadow-lg">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>

        {{-- Modifier Rôle --}}
        <div class="bg-gradient-to-br from-white to-purple-50 border border-purple-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-9 h-9 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                    <i data-lucide="shield" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Gestion du rôle</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Modifiez les permissions de l'utilisateur</p>
                </div>
            </div>

            <form method="POST" action="{{ route('users.role', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label for="role" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                        Rôle actuel
                    </label>
                    <select 
                        name="role" 
                        id="role" 
                        required 
                        class="block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-600 focus:ring-1 focus:ring-purple-600 text-sm"
                    >
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption['value'] }}" {{ $user->hasRole($roleOption['value']) ? 'selected' : '' }}>
                                {{ $roleOption['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 active:bg-purple-800 transition-all hover:shadow-lg">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Mettre à jour le rôle</span>
                </button>
            </form>
        </div>
    </div>
</x-app-layout>