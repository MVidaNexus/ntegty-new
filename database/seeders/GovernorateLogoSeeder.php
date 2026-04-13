<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GovernorateLogoSeeder extends Seeder
{
    /**
     * تعيين صور اللوجو الموجودة للمحافظات المصرية
     */
    public function run(): void
    {
        // مسار صور اللوجو - في Laravel root/images/gov
        $sourceDir = base_path('images/gov');
        $destDir = storage_path('app/public/governorate-logos');
        
        // إنشاء مجلد الوجهة إذا لم يكن موجوداً
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        // خريطة المحافظات مع الملفات
        $logoMapping = [
            'Cairo' => 'Emblem_Cairo_Governorate.jpg',
            'Giza' => 'Coat_of_arms_of_Giza_Governorate.png',
            'Alexandria' => 'Emblem_Alexandria_Governorate.jpg',
            'Dakahlia' => 'Dakahlya_Governorate_Seal_-_Egypt.svg.png',
            'Red Sea' => 'Emblem_Red_Sea_Governorate.jpg',
            'Beheira' => 'Emblem_Beheira_Governorate.jpg',
            'Fayoum' => 'Emblem_Faiyum_Governorate.jpg',
            'Gharbia' => 'Emblem_Gharbia_Governorate.jpg',
            'Ismailia' => 'Emblem_Ismailia_Governorate.jpg',
            'Monufia' => 'Emblem_Monufia_Governorate.jpg',
            'Minya' => 'Flag_of_Minya_Govenorate.JPG',
            'Qalyubia' => '120px-Emblem_Qalyubia_Governorate.jpg',
            'New Valley' => 'Emblem_New_Valley_Governorate.jpg',
            'Suez' => 'Coat_of_arms_of_Suze_Governorate.JPG',
            'Aswan' => 'Emblem_Aswan_Governorate.jpg',
            'Asyut' => 'Emblem_Asyut_Governorate.jpg',
            'Beni Suef' => 'Emblem_Beni_Suef_Governorate.jpg',
            'Port Said' => 'Emblem_Port_Said_Governorate.jpg',
            'Damietta' => 'Emblem_Damietta_Governorate.jpg',
            'Sharqia' => 'Emblem_Sharqia_Governorate.jpg',
            'South Sinai' => 'Coat_of_arms_of_South_Sinai_Governorate.png',
            'Kafr El Sheikh' => 'Emblem_Kafr_el-Sheikh_Governorate.jpg',
            'Matrouh' => 'Emblem_Matrouh_Governorate.jpg',
            'Luxor' => '120px-Emblem_Luxor_Governorate.jpg',
            'Qena' => 'Emblem_Qena_Governorate.jpg',
            'North Sinai' => 'Emblem_North_Sinai_Governorate.jpg',
            'Sohag' => 'Emblem_Sohag_Governorate.svg.png',
        ];

        $updated = 0;
        $errors = [];

        foreach ($logoMapping as $governorateName => $logoFile) {
            $sourcePath = $sourceDir . '/' . $logoFile;
            
            // البحث عن المحافظة
            $governorate = Governorate::where('name_en', $governorateName)->first();
            
            if (!$governorate) {
                $errors[] = "المحافظة غير موجودة: {$governorateName}";
                continue;
            }

            // التحقق من وجود الملف المصدر
            if (!File::exists($sourcePath)) {
                $errors[] = "الملف غير موجود: {$logoFile}";
                continue;
            }

            // إنشاء اسم ملف جديد
            $extension = pathinfo($logoFile, PATHINFO_EXTENSION);
            $newFileName = strtolower(str_replace(' ', '_', $governorateName)) . '.' . strtolower($extension);
            $destPath = $destDir . '/' . $newFileName;

            // نسخ الملف
            File::copy($sourcePath, $destPath);

            // تحديث قاعدة البيانات
            $governorate->update([
                'logo_path' => 'governorate-logos/' . $newFileName
            ]);

            $updated++;
            $this->command->info("✓ تم تعيين لوجو: {$governorate->name_ar} ({$governorateName})");
        }

        $this->command->newLine();
        $this->command->info("✓ تم تحديث {$updated} محافظة بنجاح");

        if (count($errors) > 0) {
            $this->command->newLine();
            $this->command->warn("أخطاء:");
            foreach ($errors as $error) {
                $this->command->error("  - {$error}");
            }
        }
    }
}
