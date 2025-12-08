<?php

namespace App\Jobs;

use App\Actions\Product\ImportProductsAction;
use App\Models\Company;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public Company $company,
        public User $user
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $action = new ImportProductsAction;
        $result = $action->execute($this->filePath, $this->company);

        if ($result['success']) {
            Notification::make()
                ->title('Import Complete')
                ->body("Successfully imported {$result['imported']} products. {$result['failed']} failed.")
                ->success()
                ->sendToDatabase($this->user);
        } else {
            Notification::make()
                ->title('Import Failed')
                ->body($result['message'] ?? 'An error occurred during import.')
                ->danger()
                ->sendToDatabase($this->user);
        }

        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Notification::make()
            ->title('Import Error')
            ->body('The import job failed: '.$exception->getMessage())
            ->danger()
            ->sendToDatabase($this->user);

        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }
}
