<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExamType;

class FixExamNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:exam-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove "نتائج" prefix from exam type names in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning exam types...');

        $types = ExamType::all();
        $count = 0;

        foreach ($types as $type) {
            $originalName = $type->name_ar;
            $newName = $originalName;

            // Remove 'نتائج ' prefix
            if (str_starts_with($newName, 'نتائج ')) {
                $newName = mb_substr($newName, 6); // Length of "نتائج " is 6 chars? No, wait. multibyte.
                $newName = str_replace('نتائج ', '', $originalName);
            }
            // Remove 'نتيجة ' prefix
            elseif (str_starts_with($newName, 'نتيجة ')) {
                $newName = str_replace('نتيجة ', '', $originalName);
            }

            if ($originalName !== $newName) {
                $type->name_ar = trim($newName);
                $type->save();
                $this->info("Updated: '$originalName' -> '$newName'");
                $count++;
            }
        }

        $this->info("Done. Updated $count exam types.");
    }
}
