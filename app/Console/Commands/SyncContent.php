<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncContent extends Command
{
    protected $signature = 'site:pull-live {--force : Force sync without confirmation}';
    protected $description = 'Pull content (exam_types, countries, settings) from live database to local';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will overwrite your local content tables. Continue?')) {
            return;
        }

        $this->info('Starting sync from live...');

        $tables = ['exam_types', 'countries', 'settings', 'governorates'];

        foreach ($tables as $table) {
            $this->syncTable($table);
        }

        $this->info('Sync completed successfully!');
        
        // Clear local cache to reflect changes
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $this->info('Application cache cleared.');
    }

    protected function syncTable($table)
    {
        $this->comment("Syncing table: {$table}...");

        try {
            // Get data from remote connection
            // Note: This requires the user to have Remote MySQL access enabled on their server
            // for the local machine's IP.
            $remoteData = DB::connection('remote')->table($table)->get();

            if ($remoteData->isEmpty()) {
                $this->warn("No data found in remote table: {$table}");
                return;
            }

            // Truncate local table
            Schema::disableForeignKeyConstraints();
            DB::table($table)->truncate();

            // Insert data into local table
            foreach ($remoteData as $row) {
                // Convert object to array for insertion
                $data = (array) $row;
                DB::table($table)->insert($data);
            }
            Schema::enableForeignKeyConstraints();

            $this->info("Successfully synced " . $remoteData->count() . " rows in {$table}");

        } catch (\Exception $e) {
            $this->error("Failed to sync table {$table}: " . $e->getMessage());
            $this->info("Tip: Make sure your IP is whitelisted in Remote MySQL on your server.");
        }
    }
}
