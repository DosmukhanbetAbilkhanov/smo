<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Import Products from CSV
            </h3>

            <div class="prose dark:prose-invert max-w-none mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Upload a CSV file to bulk import products. The CSV file should contain the following columns:
                </p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                    <li><strong>shop_name</strong>: Name of your shop (must exist)</li>
                    <li><strong>nomenclature_name</strong>: Approved nomenclature name</li>
                    <li><strong>name_ru</strong>: Product name in Russian</li>
                    <li><strong>name_kz</strong>: Product name in Kazakh</li>
                    <li><strong>price</strong>: Product price (numeric)</li>
                    <li><strong>quantity</strong>: Stock quantity (integer)</li>
                    <li><strong>spec_name_1, spec_value_1</strong>: First specification (optional)</li>
                    <li><strong>spec_name_2, spec_value_2</strong>: Second specification (optional)</li>
                    <li>Additional spec columns can be added as needed (spec_name_3, spec_value_3, etc.)</li>
                </ul>
            </div>

            <form wire:submit="import">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Import Products</span>
                        <span wire:loading>Processing...</span>
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if(session()->has('import_errors') && count(session('import_errors')) > 0)
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-red-900 dark:text-red-300 mb-4">
                    Import Errors ({{ count(session('import_errors')) }})
                </h3>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach(session('import_errors') as $error)
                        <div class="bg-white dark:bg-gray-800 rounded p-3 text-sm">
                            <span class="font-medium text-red-600 dark:text-red-400">Row {{ $error['row'] }}:</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $error['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
