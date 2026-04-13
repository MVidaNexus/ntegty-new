<?php

namespace App\Filament\Resources\UploadLogResource\Pages;

use App\Filament\Resources\UploadLogResource;
use App\Models\Result;
use App\Models\UploadLog;
use App\Models\ExamType;
use App\Models\Governorate;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EditUploadLog extends EditRecord
{
    protected static string $resource = UploadLogResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;
        
        // تطبيق التغييرات تلقائياً على المحافظة عند الحفظ
        if ($record->upload_type === UploadLog::TYPE_EMBED && $record->governorate_id) {
            $this->applyEmbedToGovernorate($record);
        }
        
        if ($record->upload_type === UploadLog::TYPE_PDF && $record->exam_type_id) {
            $this->applyPdfToExam($record);
        }
        
        if ($record->upload_type === UploadLog::TYPE_GOVERNORATE_TABLE && $record->exam_type_id) {
            $this->applyGovTableToExam($record);
        }
        
        if ($record->upload_type === UploadLog::TYPE_GOVERNORATE_FILE && $record->governorate_id) {
            $this->applyGovFile($record);
        }
    }
    
    protected function applyPdfToExam(UploadLog $record): void
    {
        $examType = ExamType::find($record->exam_type_id);
        $extraData = $record->extra_data ?? [];
        
        if ($examType && isset($extraData['pdf_file'])) {
            $examType->update([
                'result_service_type' => 'pdf',
                'pdf_file_path' => $extraData['pdf_file'],
            ]);
            
            Notification::make()
                ->title('تم تطبيق PDF')
                ->body('تم تحديث ملف PDF في الشهادة: ' . $examType->name_ar)
                ->success()
                ->send();
        }
    }
    
    protected function applyEmbedToGovernorate(UploadLog $record): void
    {
        // تحديث المحافظة
        $governorate = Governorate::find($record->governorate_id);
        $extraData = $record->extra_data ?? [];
        
        if ($governorate) {
            $inputType = $extraData['input_type'] ?? 'url';
            $embedCode = $inputType === 'url' 
                ? ($extraData['embed_url'] ?? '') 
                : ($extraData['embed_code'] ?? '');
            
            $governorate->update([
                'result_service_type' => 'embed',
                'embed_code' => $embedCode,
                'iframe_width' => $extraData['width'] ?? '100%',
                'iframe_height' => $extraData['height'] ?? '600px',
                'iframe_position' => $extraData['position'] ?? 'center',
                'iframe_scrolling' => $extraData['scrolling'] ?? true,
                'iframe_border' => $extraData['border'] ?? false,
                'iframe_crop_enabled' => $extraData['crop_enabled'] ?? false,
                'iframe_crop_top' => (int) ($extraData['crop_top'] ?? 0),
                'iframe_crop_right' => (int) ($extraData['crop_right'] ?? 0),
                'iframe_crop_bottom' => (int) ($extraData['crop_bottom'] ?? 0),
                'iframe_crop_left' => (int) ($extraData['crop_left'] ?? 0),
                'iframe_zoom' => (float) ($extraData['zoom'] ?? 100) / 100,
            ]);
            
            Notification::make()
                ->title('✅ تم تطبيق الـ iFrame')
                ->body('تم تحديث إعدادات iFrame للمحافظة: ' . $governorate->name_ar)
                ->success()
                ->send();
        }
    }
    
    protected function applyGovTableToExam(UploadLog $record): void
    {
        $examType = ExamType::find($record->exam_type_id);
        
        if ($examType) {
            $examType->update(['result_service_type' => 'governorate_table']);
            
            Notification::make()
                ->title('تم تطبيق جدول المحافظات')
                ->body('تم تفعيل وضع جدول المحافظات للشهادة: ' . $examType->name_ar)
                ->success()
                ->send();
        }
    }
    
    protected function applyGovFile(UploadLog $record): void
    {
        $governorate = Governorate::find($record->governorate_id);
        $extraData = $record->extra_data ?? [];
        
        if ($governorate) {
            $updateData = [];
            if (isset($extraData['pdf_file'])) {
                $updateData['result_pdf_path'] = $extraData['pdf_file'];
            }
            if (isset($extraData['is_declared'])) {
                $updateData['is_declared'] = $extraData['is_declared'];
            }
            
            if (!empty($updateData)) {
                $governorate->update($updateData);
                
                Notification::make()
                    ->title('تم تطبيق ملف المحافظة')
                    ->body('تم تحديث ملف المحافظة: ' . $governorate->name_ar)
                    ->success()
                    ->send();
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('applyToDatabase')
                ->label('تطبيق على قاعدة البيانات')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('تطبيق التغييرات')
                ->modalDescription('سيتم تطبيق إعدادات هذا السجل على الشهادة/المحافظة في قاعدة البيانات. هل تريد المتابعة؟')
                ->visible(fn ($record) => $record && in_array($record->upload_type, [
                    UploadLog::TYPE_PDF, 
                    UploadLog::TYPE_EMBED, 
                    UploadLog::TYPE_GOVERNORATE_TABLE,
                    UploadLog::TYPE_GOVERNORATE_FILE
                ]))
                ->action(function ($record) {
                    UploadLogResource::applyRecordToDatabase($record);
                    
                    Notification::make()
                        ->title('تم التطبيق بنجاح')
                        ->body('تم تطبيق الإعدادات على قاعدة البيانات')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('updateResults')
                ->label('تطبيق التعديلات على النتائج')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('تحديث النتائج المرتبطة')
                ->modalDescription('سيتم تحديث السنة الدراسية ونوع الامتحان والمحافظة والفصل الدراسي لجميع النتائج المرتبطة بهذا الملف، وإعادة حساب الحالة. هل تريد المتابعة؟')
                ->visible(fn ($record) => $record->upload_type === UploadLog::TYPE_EXCEL && $record->status === 'completed' && $record->successful_rows > 0)
                ->action(function ($record) {
                    try {
                        // Update basic fields including semester
                        $updated = Result::where('upload_log_id', $record->id)
                            ->update([
                                'academic_year_id' => $record->academic_year_id,
                                'exam_type_id' => $record->exam_type_id,
                                'governorate_id' => $record->governorate_id,
                                'branch_id' => $record->branch_id,
                                'system_type' => $record->system_type,
                                'semester' => $record->semester ?? 0,
                            ]);

                        // Recalculate status for all results
                        $examType = ExamType::find($record->exam_type_id);
                        if ($examType && $examType->auto_calculate_status) {
                            $semester = $record->semester ?? 0;
                            $statusUpdated = 0;
                            
                            Result::where('upload_log_id', $record->id)
                                ->chunkById(500, function ($results) use ($examType, $semester, &$statusUpdated) {
                                    foreach ($results as $result) {
                                        $newStatus = $examType->calculateStatus($result->total_score, $semester);
                                        if ($result->status !== $newStatus) {
                                            $result->status = $newStatus;
                                            $result->saveQuietly();
                                            $statusUpdated++;
                                        }
                                    }
                                });
                        }

                        // Clear related caches
                        Cache::flush();

                        Notification::make()
                            ->title('تم التحديث بنجاح')
                            ->body("تم تحديث {$updated} نتيجة بالبيانات الجديدة وإعادة حساب الحالة.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطأ في التحديث')
                            ->body('حدث خطأ: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('reImport')
                ->label('حذف النتائج وإعادة الاستيراد')
                ->color('danger')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->requiresConfirmation()
                ->modalHeading('⚠️ تأكيد إعادة الاستيراد')
                ->modalDescription('سيتم حذف جميع النتائج المرتبطة بهذا الملف وإعادة استيرادها بالتعيينات الجديدة. هل أنت متأكد؟')
                ->modalSubmitActionLabel('نعم، احذف وأعد الاستيراد')
                ->visible(fn ($record) => $record->upload_type === UploadLog::TYPE_EXCEL && $record->status === 'completed')
                ->disabled(fn ($record) => Cache::has("processing_upload_{$record->id}"))
                ->action(function ($record) {
                    // Check if already being processed
                    $lockKey = "processing_upload_{$record->id}";
                    if (Cache::has($lockKey)) {
                        Notification::make()
                            ->title('جاري المعالجة')
                            ->body('يتم معالجة هذا الملف حالياً، يرجى الانتظار.')
                            ->warning()
                            ->send();
                        return;
                    }

                    if (empty($record->mapping_data) || empty($record->mapping_data['seat_number'])) {
                        Notification::make()
                            ->title('خطأ في التعيين')
                            ->body('يرجى حفظ تعيين الأعمدة أولاً قبل إعادة الاستيراد.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Delete existing results for this upload
                    $deletedCount = Result::where('upload_log_id', $record->id)->delete();
                    
                    // Reset the upload log status
                    $record->update([
                        'status' => 'pending',
                        'processed_rows' => 0,
                        'successful_rows' => 0,
                        'failed_rows' => 0,
                        'records_count' => 0,
                        'error_message' => null,
                    ]);

                    // Set lock (expires in 2 hours)
                    Cache::put($lockKey, true, now()->addHours(2));

                    // Update status
                    $record->update(['status' => 'processing']);

                    // Run in background
                    $phpPath = PHP_BINARY ?: 'php';
                    $artisanPath = base_path('artisan');
                    $command = sprintf(
                        'nice -n 10 %s %s process:import %d > /dev/null 2>&1 &',
                        escapeshellarg($phpPath),
                        escapeshellarg($artisanPath),
                        $record->id
                    );
                    exec($command);

                    Notification::make()
                        ->title('بدأت إعادة الاستيراد')
                        ->body("تم حذف {$deletedCount} نتيجة سابقة وبدأت عملية الاستيراد الجديدة.")
                        ->success()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('process')
                ->label('اعتماد وبدء المعالجة')
                ->color('success')
                ->icon('heroicon-o-play')
                ->requiresConfirmation()
                ->modalHeading('بدء معالجة الملف')
                ->modalDescription('هل أنت متأكد من صحة تعيين الأعمدة؟ ستبدأ عملية الاستيراد في الخلفية ويمكنك متابعة التقدم من سجلات الرفع.')
                ->visible(fn ($record) => $record->upload_type === UploadLog::TYPE_EXCEL && $record->status === 'pending')
                ->disabled(fn ($record) => Cache::has("processing_upload_{$record->id}"))
                ->action(function ($record) {
                    // Double-check status (prevent race condition)
                    $record->refresh();
                    if ($record->status !== 'pending') {
                        Notification::make()
                            ->title('الملف قيد المعالجة')
                            ->body('هذا الملف يتم معالجته حالياً أو تمت معالجته مسبقاً.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Check if already being processed (using cache lock)
                    $lockKey = "processing_upload_{$record->id}";
                    if (Cache::has($lockKey)) {
                        Notification::make()
                            ->title('جاري المعالجة')
                            ->body('يتم معالجة هذا الملف حالياً، يرجى الانتظار.')
                            ->warning()
                            ->send();
                        return;
                    }

                    if (empty($record->mapping_data)) {
                        Notification::make()
                            ->title('خطأ في التعيين')
                            ->body('يرجى حفظ تعيين الأعمدة أولاً قبل البدء.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Set lock (expires in 2 hours)
                    Cache::put($lockKey, true, now()->addHours(2));

                    // Update status
                    $record->update(['status' => 'processing']);

                    // Run in background with low priority (no queue worker needed)
                    $phpPath = PHP_BINARY ?: 'php';
                    $artisanPath = base_path('artisan');
                    $command = sprintf(
                        'nice -n 10 %s %s process:import %d > /dev/null 2>&1 &',
                        escapeshellarg($phpPath),
                        escapeshellarg($artisanPath),
                        $record->id
                    );
                    exec($command);

                    Notification::make()
                        ->title('بدأت المعالجة')
                        ->body('تم بدء معالجة الملف في الخلفية. يمكنك متابعة التقدم من سجلات الرفع.')
                        ->success()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
