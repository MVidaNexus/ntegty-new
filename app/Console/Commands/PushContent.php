<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PushContent extends Command
{
    protected $signature = 'site:push-live {--force : Force sync without confirmation}';
    protected $description = 'Push local content (exam_types, countries, settings) to live database';

    public function handle()
    {
        $this->warn('CRITICAL: This will overwrite the content on the LIVE WEBSITE.');
        
        if (!$this->option('force') && !$this->confirm('ARE YOU ABSOLUTELY SURE? This cannot be undone.')) {
            return;
        }

        $this->info('Starting push to live site...');

        // We only push content-related tables to avoid messing up results or logs
        $tables = ['exam_types', 'countries', 'settings'];

        foreach ($tables as $table) {
            $this->pushTable($table);
        }

        $this->info('Push completed successfully!');
        
        $this->info('Note: You should clear the cache on the live server for changes to appear.');
    }

    protected function pushTable($table)
    {
        $this->comment("Pushing table: {$table}...");

        try {
            $localData = DB::table($table)->get();

            if ($localData->isEmpty()) {
                $this->warn("No data found in local table: {$table}");
                return;
            }

            // Truncate remote table
            DB::connection('remote')->table($table)->truncate();

            // Insert data into remote table
            foreach ($localData as $row) {
                $data = (array) $row;
                DB::connection('remote')->table($table)->insert($data);
            }

            $this->info("Successfully pushed " . $localData->count() . " rows to live {$table}");

        } catch (\Exception $e) {
            $this->error("Failed to push table {$table}: " . $e->getMessage());
        }
    }
}
