<?php

namespace App\Actions\Product;

use App\Enums\NomenclatureStatus;
use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportProductsAction
{
    protected array $errors = [];

    protected int $imported = 0;

    protected int $failed = 0;

    public function execute(string $filePath, Company $company): array
    {
        $this->errors = [];
        $this->imported = 0;
        $this->failed = 0;

        if (! file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File not found.',
            ];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [
                'success' => false,
                'message' => 'Unable to open file.',
            ];
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);

            return [
                'success' => false,
                'message' => 'Invalid CSV file or empty file.',
            ];
        }

        $headers = array_map('trim', $headers);

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count($row) === 1 && empty(trim($row[0]))) {
                continue;
            }

            $data = array_combine($headers, $row);

            if ($data === false) {
                $this->addError($rowNumber, 'Invalid row format - column count mismatch.');
                $this->failed++;

                continue;
            }

            $this->processRow($data, $rowNumber, $company);
        }

        fclose($handle);

        return [
            'success' => true,
            'imported' => $this->imported,
            'failed' => $this->failed,
            'errors' => $this->errors,
        ];
    }

    protected function processRow(array $data, int $rowNumber, Company $company): void
    {
        try {
            $validator = Validator::make($data, [
                'shop_name' => 'required|string',
                'nomenclature_name' => 'required|string',
                'name_ru' => 'required|string|max:255',
                'name_kz' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                $this->addError($rowNumber, $validator->errors()->first());
                $this->failed++;

                return;
            }

            $shop = Shop::where('company_id', $company->id)
                ->where('name', trim($data['shop_name']))
                ->first();

            if (! $shop) {
                $this->addError($rowNumber, "Shop '{$data['shop_name']}' not found for this company.");
                $this->failed++;

                return;
            }

            $nomenclature = Nomenclature::where('status', NomenclatureStatus::Approved)
                ->where('name_ru', trim($data['nomenclature_name']))
                ->first();

            if (! $nomenclature) {
                $this->addError($rowNumber, "Approved nomenclature '{$data['nomenclature_name']}' not found.");
                $this->failed++;

                return;
            }

            DB::beginTransaction();

            $product = Product::create([
                'shop_id' => $shop->id,
                'nomenclature_id' => $nomenclature->id,
                'name_ru' => trim($data['name_ru']),
                'name_kz' => trim($data['name_kz']),
                'price' => $data['price'],
                'quantity' => $data['quantity'],
            ]);

            $specs = $this->extractSpecs($data);
            foreach ($specs as $spec) {
                $product->specs()->create([
                    'spec_name' => $spec['name'],
                    'spec_value' => $spec['value'],
                ]);
            }

            DB::commit();

            $this->imported++;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError($rowNumber, 'Error: '.$e->getMessage());
            $this->failed++;
        }
    }

    protected function extractSpecs(array $data): array
    {
        $specs = [];
        $i = 1;

        while (isset($data["spec_name_{$i}"]) && ! empty(trim($data["spec_name_{$i}"]))) {
            $specName = trim($data["spec_name_{$i}"]);
            $specValue = isset($data["spec_value_{$i}"]) ? trim($data["spec_value_{$i}"]) : '';

            if (! empty($specName)) {
                $specs[] = [
                    'name' => $specName,
                    'value' => $specValue,
                ];
            }

            $i++;
        }

        return $specs;
    }

    protected function addError(int $row, string $message): void
    {
        $this->errors[] = [
            'row' => $row,
            'message' => $message,
        ];
    }
}
