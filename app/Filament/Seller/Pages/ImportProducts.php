<?php

namespace App\Filament\Seller\Pages;

use App\Actions\Product\ImportProductsAction;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class ImportProducts extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected string $view = 'filament.seller.pages.import-products';

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('csv_file')
                    ->label('CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                    ->maxSize(10240)
                    ->disk('local')
                    ->directory('imports')
                    ->helperText('Maximum file size: 10MB. The file must be in CSV format.')
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadSample')
                ->label('Download Sample CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $csv = "shop_name,nomenclature_name,name_ru,name_kz,price,quantity,spec_name_1,spec_value_1,spec_name_2,spec_value_2\n";
                        $csv .= "My Shop,Cement,Portland Cement 400,Портланд цемент 400,5000.00,100,Weight,50kg,Type,M400\n";
                        $csv .= "My Shop,Bricks,Red Brick,Красный кирпич,25.50,5000,Size,250x120x65mm,Material,Ceramic\n";
                        echo $csv;
                    }, 'product-import-sample.csv', [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
        ];
    }

    public function import(): void
    {
        $data = $this->form->getState();

        if (! isset($data['csv_file'])) {
            Notification::make()
                ->title('No file selected')
                ->danger()
                ->send();

            return;
        }

        try {
            $company = Filament::getTenant();
            $filePath = Storage::disk('local')->path($data['csv_file']);

            $action = new ImportProductsAction;
            $result = $action->execute($filePath, $company);

            if ($result['success']) {
                Notification::make()
                    ->title('Import Successful')
                    ->body("Imported {$result['imported']} products successfully. {$result['failed']} failed.")
                    ->success()
                    ->send();

                if (! empty($result['errors'])) {
                    session()->put('import_errors', $result['errors']);
                }

                $this->form->fill();
            } else {
                Notification::make()
                    ->title('Import Failed')
                    ->body($result['message'] ?? 'An error occurred during import.')
                    ->danger()
                    ->send();
            }
        } catch (Halt $exception) {
            return;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
