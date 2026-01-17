<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900">Utilisateurs</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        {{-- En-tête --}}
        <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div class="min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Gestion des utilisateurs</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $users->total() }} utilisateur(s)</p>
                </div>
                <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-gray-900 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-800 active:bg-gray-950 transition-all hover:shadow-lg hover:scale-105 w-full sm:w-auto flex-shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span class="hidden xs:inline">Nouvel utilisateur</span>
                    <span class="xs:hidden">Nouveau</span>
                </a>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                <div>
                    <label for="search" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Rechercher
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom ou email..." class="block w-full pl-10 py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Rôle
                    </label>
                    <select name="role" id="role" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="vendeur" {{ request('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 sm:py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 active:bg-gray-950 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Filtrer</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Vue Desktop (Tableau) --}}
        <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateur</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rôle</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date création</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 xl:px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-900 to-gray-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                                            <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 xl:px-6 py-4">
                                    @foreach($user->roles as $role)
                                        @if($role->name === 'admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                <i data-lucide="shield" class="w-3 h-3"></i>
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                <i data-lucide="user" class="w-3 h-3"></i>
                                                Vendeur
                                            </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-4 xl:px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $user->created_at->format('d/m/Y à H:i') }}
                                </td>
                                <td class="px-4 xl:px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 hover:text-blue-900 border border-blue-200 hover:border-blue-300 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                            <span class="hidden xl:inline">Modifier</span>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 hover:text-red-900 border border-red-200 hover:border-red-300 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span class="hidden xl:inline">Supprimer</span>
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
                                        <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mb-3">
                                            <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">Aucun utilisateur trouvé</p>
                                        <p class="text-xs text-gray-500 mt-1">Les utilisateurs apparaîtront ici</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-4 xl:px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Vue Mobile/Tablet (Cards) --}}
        <div class="lg:hidden space-y-3">
            @forelse($users as $user)
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all">
                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-100">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-900 to-gray-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <span class="text-white text-base font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                            <div class="mt-1.5">
                                @foreach($user->roles as $role)
                                    @if($role->name === 'admin')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                            <i data-lucide="shield" class="w-3 h-3"></i>
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            <i data-lucide="user" class="w-3 h-3"></i>
                                            Vendeur
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex items-center gap-2 mb-3">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                        <span class="text-xs text-gray-600">Créé le {{ $user->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-blue-700 border border-blue-200 hover:bg-blue-50 active:bg-blue-100 rounded-lg transition-colors">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            <span>Modifier</span>
                        </a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="flex-1" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-red-700 border border-red-200 hover:bg-red-50 active:bg-red-100 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Supprimer</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                    <div class="w-12 h-12 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="users" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Aucun utilisateur trouvé</p>
                    <p class="text-xs text-gray-500 mt-1">Les utilisateurs apparaîtront ici</p>
                    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 active:bg-gray-950 transition-colors mt-4">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Ajouter un utilisateur
                    </a>
                </div>
            @endforelse

            @if($users->hasPages())
                <div class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>