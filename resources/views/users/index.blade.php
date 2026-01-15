<x-app-layout>
    <x-slot name="header">
        Utilisateurs
    </x-slot>

    <div class="space-y-6">
        {{-- En-tête avec boutons actions --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Gestion des utilisateurs</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $users->total() }} utilisateur(s)</p>
                </div>
                <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition-colors">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Nouvel utilisateur
                </a>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Rechercher
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom ou email..." class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                </div>

                <div>
                    <label for="role" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Rôle
                    </label>
                    <select name="role" id="role" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="vendeur" {{ request('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition-colors">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        {{-- Liste des utilisateurs --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date création</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-sm font-semibold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($user->roles as $role)
                                        @if($role->name === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                                Vendeur
                                            </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d/m/Y à H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900" title="Modifier">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="users" class="w-12 h-12 text-gray-400 mb-3"></i>
                                        <p class="text-sm font-medium text-gray-900">Aucun utilisateur trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
