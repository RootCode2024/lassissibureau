<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Importer des données</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Importez vos modèles, revendeurs et produits en quelques clics</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Feedback Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 sm:mb-6 rounded-xl border border-green-200 bg-green-50 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-green-900 break-words">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="flex-shrink-0 text-green-600 hover:text-green-800">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true, expanded: false }" 
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="mb-4 sm:mb-6 rounded-xl border border-red-200 bg-red-50 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-red-900 break-words">{{ session('error') }}</p>
                            @if(session('import_errors'))
                                <button @click="expanded = !expanded" 
                                        class="mt-2 text-xs font-medium text-red-700 hover:text-red-800 inline-flex items-center gap-1">
                                    <span x-text="expanded ? 'Masquer les détails' : 'Voir les détails'"></span>
                                    <svg class="h-4 w-4 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="expanded" 
                                     x-transition
                                     class="mt-3 rounded-lg bg-white p-3 text-xs max-h-48 overflow-y-auto border border-red-100">
                                    <ul class="space-y-1.5">
                                        @foreach(session('import_errors') as $failure)
                                            <li class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2">
                                                <span class="flex-shrink-0 font-semibold text-red-700">Ligne {{ $failure->row() }}:</span>
                                                <span class="text-gray-700 break-words">{{ implode(', ', $failure->errors()) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <button @click="show = false" class="flex-shrink-0 text-red-600 hover:text-red-800">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Steps Progress Indicator - RESPONSIVE --}}
            <div class="mb-6 sm:mb-8 rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm overflow-x-auto">
                <!-- Mobile: Vertical Stack -->
                <div class="sm:hidden space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 flex-shrink-0">1</div>
                        <span class="text-sm font-medium text-gray-900">Modèles</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-sm font-semibold text-amber-700 flex-shrink-0">2</div>
                        <span class="text-sm font-medium text-gray-900">Revendeurs</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700 flex-shrink-0">3</div>
                        <span class="text-sm font-medium text-gray-900">Stock</span>
                    </div>
                    <p class="text-xs text-gray-500 pt-2 border-t border-gray-100">Ordre recommandé</p>
                </div>

                <!-- Tablet & Desktop: Horizontal -->
                <div class="hidden sm:flex items-center justify-between">
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">1</div>
                            <span class="text-sm font-medium text-gray-900">Modèles</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-sm font-semibold text-amber-700">2</div>
                            <span class="text-sm font-medium text-gray-900">Revendeurs</span>
                        </div>
                        <svg class="h-5 w-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">3</div>
                            <span class="text-sm font-medium text-gray-900">Stock</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 hidden lg:block">Ordre recommandé</p>
                </div>
            </div>

            {{-- Import Cards Grid - RESPONSIVE --}}
            <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">

                {{-- 1. MODELS --}}
                <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:border-blue-300 hover:shadow-md">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="border-b border-gray-100 bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-blue-500 shadow-sm flex-shrink-0">
                                        <i data-lucide="tag" class="h-4 w-4 sm:h-5 sm:w-5 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Modèles</h3>
                                        <p class="text-xs text-gray-600 truncate">Catalogue de référence</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-blue-100 px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-xs font-medium text-blue-700 whitespace-nowrap flex-shrink-0">Étape 1</span>
                            </div>
                        </div>
                        
                        <div class="p-4 sm:p-5">
                            <form action="{{ route('imports.models') }}" method="POST" enctype="multipart/form-data" 
                                  x-data="{ uploading: false, fileName: '' }"
                                  @submit="uploading = true"
                                  class="space-y-3 sm:space-y-4">
                                @csrf
                                
                                <div>
                                    <label class="relative block cursor-pointer">
                                        <input type="file" 
                                               name="file" 
                                               accept=".xlsx,.xls,.csv" 
                                               required
                                               @change="fileName = $event.target.files[0]?.name || ''"
                                               class="hidden">
                                        <div class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 sm:p-6 transition-colors hover:border-blue-400 hover:bg-blue-50/50"
                                             :class="fileName && 'border-blue-400 bg-blue-50/50'">
                                            <div class="text-center">
                                                <i data-lucide="upload" class="mx-auto h-7 w-7 sm:h-8 sm:w-8 text-gray-400 mb-2"></i>
                                                <p class="text-xs sm:text-sm font-medium text-gray-700 break-all px-2" x-text="fileName || 'Choisir un fichier'"></p>
                                                <p class="mt-1 text-[10px] sm:text-xs text-gray-500">.xlsx, .xls, .csv</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-2.5 sm:p-3">
                                    <p class="text-[10px] sm:text-xs font-medium text-gray-700 mb-1.5">Colonnes requises</p>
                                    <div class="flex flex-wrap gap-1 sm:gap-1.5">
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">marque</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">modele</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">categorie</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">prix_vente</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">prix_achat</code>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 pt-2">
                                    <a href="{{ route('imports.template', ['type' => 'models']) }}" 
                                       class="inline-flex items-center justify-center sm:justify-start gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                        <i data-lucide="download" class="h-3.5 w-3.5"></i>
                                        Modèle Excel
                                    </a>
                                    <button type="submit" 
                                            :disabled="uploading"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                                        <span x-show="!uploading">Importer</span>
                                        <span x-show="uploading" class="flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Import...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 2. RESELLERS --}}
                <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:border-amber-300 hover:shadow-md">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="border-b border-gray-100 bg-gradient-to-br from-amber-50 to-amber-100/50 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-amber-500 shadow-sm flex-shrink-0">
                                        <i data-lucide="users" class="h-4 w-4 sm:h-5 sm:w-5 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Revendeurs</h3>
                                        <p class="text-xs text-gray-600 truncate">Base partenaires</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-amber-100 px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-xs font-medium text-amber-700 whitespace-nowrap flex-shrink-0">Étape 2</span>
                            </div>
                        </div>
                        
                        <div class="p-4 sm:p-5">
                            <form action="{{ route('imports.resellers') }}" method="POST" enctype="multipart/form-data"
                                  x-data="{ uploading: false, fileName: '' }"
                                  @submit="uploading = true"
                                  class="space-y-3 sm:space-y-4">
                                @csrf
                                
                                <div>
                                    <label class="relative block cursor-pointer">
                                        <input type="file" 
                                               name="file" 
                                               accept=".xlsx,.xls,.csv" 
                                               required
                                               @change="fileName = $event.target.files[0]?.name || ''"
                                               class="hidden">
                                        <div class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 sm:p-6 transition-colors hover:border-amber-400 hover:bg-amber-50/50"
                                             :class="fileName && 'border-amber-400 bg-amber-50/50'">
                                            <div class="text-center">
                                                <i data-lucide="upload" class="mx-auto h-7 w-7 sm:h-8 sm:w-8 text-gray-400 mb-2"></i>
                                                <p class="text-xs sm:text-sm font-medium text-gray-700 break-all px-2" x-text="fileName || 'Choisir un fichier'"></p>
                                                <p class="mt-1 text-[10px] sm:text-xs text-gray-500">.xlsx, .xls, .csv</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-2.5 sm:p-3">
                                    <p class="text-[10px] sm:text-xs font-medium text-gray-700 mb-1.5">Colonnes requises</p>
                                    <div class="flex flex-wrap gap-1 sm:gap-1.5">
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">nom</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">telephone_1</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">...</code>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 pt-2">
                                    <a href="{{ route('imports.template', ['type' => 'resellers']) }}" 
                                       class="inline-flex items-center justify-center sm:justify-start gap-1.5 text-xs font-medium text-amber-600 hover:text-amber-700 transition-colors">
                                        <i data-lucide="download" class="h-3.5 w-3.5"></i>
                                        Modèle Excel
                                    </a>
                                    <button type="submit"
                                            :disabled="uploading"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition-all hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                                        <span x-show="!uploading">Importer</span>
                                        <span x-show="uploading" class="flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Import...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 3. PRODUCTS --}}
                <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:border-green-300 hover:shadow-md">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="border-b border-gray-100 bg-gradient-to-br from-green-50 to-green-100/50 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-green-500 shadow-sm flex-shrink-0">
                                        <i data-lucide="smartphone" class="h-4 w-4 sm:h-5 sm:w-5 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Stock</h3>
                                        <p class="text-xs text-gray-600 truncate">Produits physiques</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-green-100 px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-xs font-medium text-green-700 whitespace-nowrap flex-shrink-0">Étape 3</span>
                            </div>
                        </div>
                        
                        <div class="p-4 sm:p-5">
                            <form action="{{ route('imports.products') }}" method="POST" enctype="multipart/form-data"
                                  x-data="{ uploading: false, fileName: '' }"
                                  @submit="uploading = true"
                                  class="space-y-3 sm:space-y-4">
                                @csrf
                                
                                <div>
                                    <label class="relative block cursor-pointer">
                                        <input type="file" 
                                               name="file" 
                                               accept=".xlsx,.xls,.csv" 
                                               required
                                               @change="fileName = $event.target.files[0]?.name || ''"
                                               class="hidden">
                                        <div class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 sm:p-6 transition-colors hover:border-green-400 hover:bg-green-50/50"
                                             :class="fileName && 'border-green-400 bg-green-50/50'">
                                            <div class="text-center">
                                                <i data-lucide="upload" class="mx-auto h-7 w-7 sm:h-8 sm:w-8 text-gray-400 mb-2"></i>
                                                <p class="text-xs sm:text-sm font-medium text-gray-700 break-all px-2" x-text="fileName || 'Choisir un fichier'"></p>
                                                <p class="mt-1 text-[10px] sm:text-xs text-gray-500">.xlsx, .xls, .csv</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-2.5 sm:p-3">
                                    <p class="text-[10px] sm:text-xs font-medium text-gray-700 mb-1.5">Colonnes requises</p>
                                    <div class="flex flex-wrap gap-1 sm:gap-1.5">
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">marque</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">modele</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">imei</code>
                                        <code class="rounded bg-white px-1.5 sm:px-2 py-0.5 text-[10px] sm:text-xs font-mono text-gray-800 border border-gray-200">...</code>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 pt-2">
                                    <a href="{{ route('imports.template', ['type' => 'products']) }}" 
                                       class="inline-flex items-center justify-center sm:justify-start gap-1.5 text-xs font-medium text-green-600 hover:text-green-700 transition-colors">
                                        <i data-lucide="download" class="h-3.5 w-3.5"></i>
                                        Modèle Excel
                                    </a>
                                    <button type="submit"
                                            :disabled="uploading"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition-all hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                                        <span x-show="!uploading">Importer</span>
                                        <span x-show="uploading" class="flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Import...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Instructions Card - RESPONSIVE --}}
            <div class="mt-6 sm:mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="h-4 w-4 sm:h-5 sm:w-5 text-gray-600 flex-shrink-0"></i>
                        <h3 class="font-semibold text-gray-900 text-sm sm:text-base">Guide d'importation</h3>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid gap-3 sm:gap-4 md:grid-cols-2">
                        <div class="flex gap-2 sm:gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">1</div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-medium text-gray-900">Format des fichiers</h4>
                                <p class="mt-1 text-xs sm:text-sm text-gray-600">La première ligne doit contenir les noms de colonnes exacts (minuscules, sans accents).</p>
                            </div>
                        </div>
                        <div class="flex gap-2 sm:gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">2</div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-medium text-gray-900">Ordre d'import</h4>
                                <p class="mt-1 text-xs sm:text-sm text-gray-600">Importez dans l'ordre : Modèles → Revendeurs → Stock pour éviter les erreurs.</p>
                            </div>
                        </div>
                        <div class="flex gap-2 sm:gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">3</div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-medium text-gray-900">Création automatique</h4>
                                <p class="mt-1 text-xs sm:text-sm text-gray-600">Les marques et modèles inexistants seront créés automatiquement lors de l'import du stock.</p>
                            </div>
                        </div>
                        <div class="flex gap-2 sm:gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">4</div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs sm:text-sm font-medium text-gray-900">IMEI uniques</h4>
                                <p class="mt-1 text-xs sm:text-sm text-gray-600">Les IMEI doivent être uniques. Les doublons seront automatiquement rejetés.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // Initialiser Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @endpush
</x-app-layout>