<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name_ar',
        'name_en',
        'code',
        'slug',
        'level',
        'total_score',
        'passing_score',
        'second_round_threshold',
        'auto_calculate_status',
        // Semester settings
        'has_semester_settings',
        'semester1_total_score',
        'semester1_passing_score',
        'semester1_second_round',
        'semester2_total_score',
        'semester2_passing_score',
        'semester2_second_round',
        // System type settings (old/new for secondary)
        'has_system_type_settings',
        'old_system_total_score',
        'old_system_passing_score',
        'old_system_second_round',
        'new_system_total_score',
        'new_system_passing_score',
        'new_system_second_round',
        'excluded_subjects',
        'absent_markers',
        'result_service_type',
        'embed_code',
        'pdf_file_path',
        // iframe settings
        'iframe_width',
        'iframe_height',
        'iframe_position',
        'iframe_scrolling',
        'iframe_border',
        // iframe crop settings
        'iframe_crop_enabled',
        'iframe_crop_top',
        'iframe_crop_right',
        'iframe_crop_bottom',
        'iframe_crop_left',
        'iframe_zoom',
        // SEO fields
        'seo_title',
        'seo_description',
        'seo_keywords',
        // Content fields
        'content_title',
        'content_intro',
        'content_body',
        'content_table_html',
        'show_content_section',
        'show_popular_searches',
        'popular_searches',
        // Result approval fields
        'is_result_approved',
        'result_announcement_text',
    ];

    protected $casts = [
        'auto_calculate_status' => 'boolean',
        'has_semester_settings' => 'boolean',
        'show_content_section' => 'boolean',
        'is_result_approved' => 'boolean',
        'show_popular_searches' => 'boolean',
        'iframe_scrolling' => 'boolean',
        'iframe_border' => 'boolean',
        'iframe_crop_enabled' => 'boolean',
        'total_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'second_round_threshold' => 'decimal:2',
        'semester1_total_score' => 'decimal:2',
        'semester1_passing_score' => 'decimal:2',
        'semester1_second_round' => 'decimal:2',
        'semester2_total_score' => 'decimal:2',
        'semester2_passing_score' => 'decimal:2',
        'semester2_second_round' => 'decimal:2',
        'has_system_type_settings' => 'boolean',
        'old_system_total_score' => 'decimal:2',
        'old_system_passing_score' => 'decimal:2',
        'old_system_second_round' => 'decimal:2',
        'new_system_total_score' => 'decimal:2',
        'new_system_passing_score' => 'decimal:2',
        'new_system_second_round' => 'decimal:2',
        'excluded_subjects' => \App\Casts\JsonWithUnicode::class,
        'absent_markers' => \App\Casts\JsonWithUnicode::class,
        'popular_searches' => \App\Casts\JsonWithUnicode::class,
    ];

    /**
     * Default absent markers used when no custom markers are defined
     */
    public const DEFAULT_ABSENT_MARKERS = [
        'غ', 'غ.', 'غائب', 'غائبة', 'غياب', 'غایب',
        '-', '--', '---',
        'absent', 'abs',
        'محروم', 'مغترب',
    ];

    /**
     * Get formatted content body with auto-converted tables
     * Converts pipe-separated text to tables in both content_body and content_table_html
     * Preserves existing HTML (headers, paragraphs, etc.)
     */
    public function getFormattedContentBody(): string
    {
        $content = $this->content_body ?? '';
        $tableHtml = trim($this->content_table_html ?? '');
        
        // Check if content_body has pipe-separated text that should be a table
        if (str_contains(strip_tags($content), '|')) {
            $content = $this->extractAndConvertPipeTables($content);
        }
        
        // If table field has content, process it
        if (!empty($tableHtml)) {
            if (str_contains($tableHtml, '<table')) {
                $content .= "\n" . $tableHtml;
            } elseif (str_contains($tableHtml, '|')) {
                $table = $this->buildTableFromPipeText($tableHtml);
                $content .= "\n" . $table;
            } else {
                $content .= "\n" . $tableHtml;
            }
        }
        
        return $content;
    }

    /**
     * Convert each group of consecutive pipe-separated lines to a table IN PLACE
     * Multiple tables stay in their original positions
     */
    protected function extractAndConvertPipeTables(string $content): string
    {
        // Split content by paragraphs to find groups
        // Pattern: find paragraph or text containing pipe
        $pattern = '/<p[^>]*>([^<]*\|[^<]*)<\/p>/u';
        
        // Find all paragraphs with pipes
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        if (empty($matches)) {
            // Try simpler pattern for pipe text not in <p>
            preg_match_all('/([^<>\n]*\|[^<>\n]*)/u', $content, $simpleMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            if (empty($simpleMatches)) {
                return $content;
            }
            $matches = $simpleMatches;
        }
        
        // Group consecutive pipe lines (within 500 chars of each other = same table)
        $groups = [];
        $currentGroup = [];
        $lastOffset = -1000;
        
        foreach ($matches as $match) {
            $text = is_array($match[1]) ? $match[1][0] : (isset($match[1]) ? $match[1] : $match[0][0]);
            $offset = is_array($match[0]) ? $match[0][1] : 0;
            $fullMatch = is_array($match[0]) ? $match[0][0] : $match[0];
            
            $text = trim($text);
            if (empty($text) || !str_contains($text, '|')) continue;
            
            // If this line is close to the last one, it's the same table
            if ($offset - $lastOffset < 500 && !empty($currentGroup)) {
                $currentGroup[] = ['text' => $text, 'full' => $fullMatch];
            } else {
                // Start new group
                if (count($currentGroup) >= 2) {
                    $groups[] = $currentGroup;
                }
                $currentGroup = [['text' => $text, 'full' => $fullMatch]];
            }
            $lastOffset = $offset + strlen($fullMatch);
        }
        
        // Don't forget last group
        if (count($currentGroup) >= 2) {
            $groups[] = $currentGroup;
        }
        
        // Convert each group to a table IN PLACE
        foreach ($groups as $group) {
            $pipeLines = array_map(fn($item) => $item['text'], $group);
            $table = $this->buildTableHtml($pipeLines);
            
            // Replace first occurrence with table
            $firstFull = $group[0]['full'];
            $content = preg_replace('/' . preg_quote($firstFull, '/') . '/u', $table, $content, 1);
            
            // Remove rest of the group
            for ($i = 1; $i < count($group); $i++) {
                $content = preg_replace('/' . preg_quote($group[$i]['full'], '/') . '/u', '', $content, 1);
            }
        }
        
        // Clean up empty tags and extra whitespace
        $content = preg_replace('/<p[^>]*>\s*<\/p>/u', '', $content);
        $content = preg_replace('/<p[^>]*><br\s*\/?><\/p>/u', '', $content);
        $content = preg_replace('/<strong>\s*<\/strong>/u', '', $content);
        $content = preg_replace('/(<br\s*\/?>\s*){2,}/u', '<br>', $content);
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        return $content;
    }

    /**
     * Build table from plain pipe-separated text (for the separate table field)
     */
    protected function buildTableFromPipeText(string $text): string
    {
        $lines = explode("\n", trim($text));
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, fn($line) => !empty($line) && str_contains($line, '|'));
        
        if (count($lines) < 2) {
            return $text;
        }
        
        return $this->buildTableHtml($lines);
    }

    /**
     * Convert pipe-separated text blocks to HTML tables
     * Detects patterns like: "header1 | header2\ndata1 | data2"
     * Preserves existing HTML (h2, h3, strong, etc.)
     */
    protected function convertPipeTextToTable(string $content): string
    {
        // Check if there are pipe patterns
        if (!str_contains(strip_tags($content), '|')) {
            return $content;
        }
        
        // Find blocks that contain pipe-separated lines and are not headers
        // We'll look for patterns like: text | text (with potential newlines between)
        
        // First, let's identify <p> tags or plain text that contain pipe patterns
        // Pattern to match paragraph content with pipes
        $pattern = '/(<p[^>]*>)((?:[^<]*\|[^<]*)+)(<\/p>)/';
        
        // Collect all matching paragraph contents
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            // No paragraph-wrapped pipe content, check for plain text
            // Look for consecutive lines with pipes in the original content
            $plainText = strip_tags($content);
            if (str_contains($plainText, '|')) {
                $lines = preg_split('/\r?\n/', $plainText);
                $lines = array_map('trim', $lines);
                $pipeLines = array_filter($lines, fn($line) => str_contains($line, '|') && !empty($line));
                
                if (count($pipeLines) >= 2) {
                    // Build table and append to existing content
                    $table = $this->buildTableHtml(array_values($pipeLines));
                    
                    // Replace the pipe-text portion with the table
                    foreach ($pipeLines as $line) {
                        // Remove this line from content
                        $escapedLine = preg_quote($line, '/');
                        $content = preg_replace('/<p[^>]*>\s*' . $escapedLine . '\s*<\/p>/', '', $content, 1);
                    }
                    
                    $content .= $table;
                }
            }
            return $content;
        }
        
        // Collect all pipe-lines from paragraphs
        $pipeLines = [];
        $paragraphsToRemove = [];
        
        foreach ($matches as $match) {
            $pContent = html_entity_decode(strip_tags($match[2]));
            if (str_contains($pContent, '|')) {
                $pipeLines[] = trim($pContent);
                $paragraphsToRemove[] = $match[0]; // Full paragraph to remove
            }
        }
        
        if (count($pipeLines) >= 2) {
            // Build the table
            $table = $this->buildTableHtml($pipeLines);
            
            // Remove the original paragraphs containing pipe text
            foreach ($paragraphsToRemove as $para) {
                $content = str_replace($para, '', $content);
            }
            
            // Append the table
            $content .= $table;
        }
        
        return $content;
    }

    /**
     * Build HTML table from array of pipe-separated lines
     */
    protected function buildTableHtml(array $lines): string
    {
        $html = '<table>';
        
        foreach ($lines as $index => $line) {
            $cells = array_map('trim', explode('|', $line));
            $cells = array_filter($cells, fn($c) => $c !== ''); // Remove empty cells
            
            if (empty($cells)) continue;
            
            // First line is header
            if ($index === 0) {
                $html .= '<thead><tr>';
                foreach ($cells as $cell) {
                    $html .= '<th>' . htmlspecialchars($cell) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
            } else {
                $html .= '<tr>';
                foreach ($cells as $cell) {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }

    /**
     * Get result service type label
     */
    public function getResultServiceTypeLabel(): string
    {
        return match($this->result_service_type) {
            'search' => 'البحث برقم الجلوس',
            'embed' => 'إيفريم خارجي',
            'pdf' => 'ملف PDF',
            'governorate_table' => 'جدول المحافظات',
            default => 'البحث برقم الجلوس',
        };
    }

    /**
     * Check if result service is search type
     */
    public function isSearchService(): bool
    {
        return $this->result_service_type === 'search' || empty($this->result_service_type);
    }

    /**
     * Check if result service is embed type
     */
    public function isEmbedService(): bool
    {
        return $this->result_service_type === 'embed';
    }

    /**
     * Check if result service is PDF type
     */
    public function isPdfService(): bool
    {
        return $this->result_service_type === 'pdf';
    }

    /**
     * Check if result service is governorate table type
     */
    public function isGovernorateTableService(): bool
    {
        return $this->result_service_type === 'governorate_table';
    }

    /**
     * Check if this exam type supports governorate table view
     * Unified exams (diplomas, thanaweya, baccalaureate) don't support this
     */
    public function supportsGovernorateTable(): bool
    {
        // Check if it's a unified exam type (doesn't have governorate-specific results)
        $unifiedTypes = [
            'diploma', 'thanaweya', 'baccalaureate', 'thanawya', 'secondary',
            'ثانوية', 'دبلوم', 'بكالوريا'
        ];
        
        $codeOrSlug = strtolower($this->code . ' ' . $this->slug . ' ' . $this->name_ar);
        
        foreach ($unifiedTypes as $type) {
            if (str_contains($codeOrSlug, $type)) {
                return false;
            }
        }
        
        return true;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(ExamBranch::class)->orderBy('sort_order');
    }

    public function columnMappings(): HasMany
    {
        return $this->hasMany(ColumnMapping::class);
    }

    /**
     * Check if this exam type has branches
     */
    public function hasBranches(): bool
    {
        return $this->branches()->where('is_active', true)->exists();
    }

    /**
     * Get settings based on semester
     * @param int $semester 0 = both (default), 1 = first, 2 = second
     * @return array{total_score: float|null, passing_score: float|null, second_round: float|null}
     */
    public function getSettingsForSemester(int $semester = 0): array
    {
        // If semester settings are not enabled, use default values
        if (!$this->has_semester_settings) {
            return [
                'total_score' => $this->total_score,
                'passing_score' => $this->passing_score,
                'second_round' => $this->second_round_threshold,
            ];
        }

        return match ($semester) {
            1 => [ // First semester
                'total_score' => $this->semester1_total_score ?? $this->total_score,
                'passing_score' => $this->semester1_passing_score ?? $this->passing_score,
                'second_round' => $this->semester1_second_round ?? $this->second_round_threshold,
            ],
            2 => [ // Second semester
                'total_score' => $this->semester2_total_score ?? $this->total_score,
                'passing_score' => $this->semester2_passing_score ?? $this->passing_score,
                'second_round' => $this->semester2_second_round ?? $this->second_round_threshold,
            ],
            default => [ // Both semesters (full year)
                'total_score' => $this->total_score,
                'passing_score' => $this->passing_score,
                'second_round' => $this->second_round_threshold,
            ],
        };
    }

    /**
     * Get settings based on system type (old/new for secondary education)
     * @param string|null $systemType 'old' or 'new' or null for default
     * @return array{total_score: float|null, passing_score: float|null, second_round: float|null}
     */
    public function getSettingsForSystemType(?string $systemType = null): array
    {
        // If system type settings are not enabled, use default values
        if (!$this->has_system_type_settings || !$systemType) {
            return [
                'total_score' => $this->total_score,
                'passing_score' => $this->passing_score,
                'second_round' => $this->second_round_threshold,
            ];
        }

        return match ($systemType) {
            'old' => [ // النظام القديم
                'total_score' => $this->old_system_total_score ?? $this->total_score,
                'passing_score' => $this->old_system_passing_score ?? $this->passing_score,
                'second_round' => $this->old_system_second_round ?? $this->second_round_threshold,
            ],
            'new' => [ // النظام الحديث
                'total_score' => $this->new_system_total_score ?? $this->total_score,
                'passing_score' => $this->new_system_passing_score ?? $this->passing_score,
                'second_round' => $this->new_system_second_round ?? $this->second_round_threshold,
            ],
            default => [ // Default
                'total_score' => $this->total_score,
                'passing_score' => $this->passing_score,
                'second_round' => $this->second_round_threshold,
            ],
        };
    }

    /**
     * Calculate student status based on total score
     * @param float|null $totalScore The student's total score
     * @param int $semester 0 = both (default), 1 = first, 2 = second
     */
    public function calculateStatus(?float $totalScore, int $semester = 0): string
    {
        if (!$this->auto_calculate_status || $totalScore === null) {
            return 'غير محدد';
        }

        $settings = $this->getSettingsForSemester($semester);
        $passingScore = $settings['passing_score'];
        $secondRound = $settings['second_round'];

        if ($passingScore) {
            if ($totalScore >= $passingScore) {
                return 'ناجح';
            }
            
            if ($secondRound && $totalScore >= $secondRound) {
                return 'دور ثاني';
            }
            
            return 'راسب';
        }

        return 'غير محدد';
    }

    /**
     * Calculate total score from subjects data, excluding specified subjects
     */
    public function calculateTotalScore(array $subjectsData): float
    {
        $excludedSubjects = $this->excluded_subjects;
        if (is_string($excludedSubjects)) {
            $decoded = json_decode($excludedSubjects, true);
            if (is_array($decoded)) {
                $excludedSubjects = $decoded;
            } else {
                $excludedSubjects = array_map('trim', explode(',', $excludedSubjects));
            }
        }
        if (!is_array($excludedSubjects)) {
            $excludedSubjects = [];
        }
        
        $total = 0;
        
        $normalizeArabic = function($text) {
            $text = mb_strtolower(trim($text));
            // Remove newlines and extra spaces
            $text = preg_replace('/[\r\n\t]+/', ' ', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            $text = str_replace('ى', 'ي', $text);
            $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
            $text = str_replace('ة', 'ه', $text);
            return $text;
        };

        // Check if جبر or هندسة exists - if so, exclude رياضيات
        $hasAlgebra = false;
        $hasGeometry = false;
        foreach ($subjectsData as $subject => $score) {
            $subjectNorm = $normalizeArabic($subject);
            if (in_array($subjectNorm, ['جبر', 'الجبر', 'gbr', 'algebra'])) {
                $numericScore = $this->parseScoreValue($score);
                if ($numericScore !== null && $numericScore > 0) {
                    $hasAlgebra = true;
                }
            }
            if (in_array($subjectNorm, ['هندسه', 'الهندسه', 'hands', 'geometry'])) {
                $numericScore = $this->parseScoreValue($score);
                if ($numericScore !== null && $numericScore > 0) {
                    $hasGeometry = true;
                }
            }
        }

        foreach ($subjectsData as $subject => $score) {
            $subjectNorm = $normalizeArabic($subject);
            if (in_array($subjectNorm, ['الاداره', 'الاداره التعليميه', 'المدرسه', 'الاسم', 'رقم الجلوس', 'المجموع', 'المجموع الكلي'])) {
                continue;
            }
            
            // Skip any subject containing رياضيات if جبر or هندسة exist
            if (($hasAlgebra || $hasGeometry) && (
                str_contains($subjectNorm, 'رياضيات') || 
                str_contains($subjectNorm, 'الرياضيات') ||
                in_array($subjectNorm, ['رياضيات', 'الرياضيات', 'math', 'maths', 'mathematics'])
            )) {
                continue;
            }
            
            $isExcluded = false;
            
            foreach ($excludedSubjects as $excluded) {
                if ($normalizeArabic($excluded) === $subjectNorm) {
                    $isExcluded = true;
                    break;
                }
            }
            
            if (!$isExcluded) {
                // Parse the score value, handling special cases
                $numericScore = $this->parseScoreValue($score);
                if ($numericScore !== null) {
                    $total += $numericScore;
                }
            }
        }

        return $total;
    }

    /**
     * Get the absent markers for this exam type
     * Returns custom markers if defined, otherwise default markers
     */
    public function getAbsentMarkers(): array
    {
        $customMarkers = $this->absent_markers;
        
        if (!empty($customMarkers) && is_array($customMarkers)) {
            return $customMarkers;
        }
        
        return self::DEFAULT_ABSENT_MARKERS;
    }

    /**
     * Check if a value is an absent marker
     */
    public function isAbsentMarker($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        
        $value = trim((string) $value);
        $valueLower = mb_strtolower($value);
        $valueLower = str_replace(['أ', 'إ', 'آ'], 'ا', $valueLower);
        
        foreach ($this->getAbsentMarkers() as $marker) {
            $markerLower = mb_strtolower(trim($marker));
            $markerLower = str_replace(['أ', 'إ', 'آ'], 'ا', $markerLower);
            
            if ($valueLower === $markerLower) {
                return true;
            }
        }
        
        // Also check if it starts with غ
        if (mb_substr($value, 0, 1) === 'غ') {
            return true;
        }
        
        return false;
    }

    /**
     * Parse a score value, handling various formats including absence markers
     * Returns null if the score should be skipped (like metadata fields)
     * Returns 0 for absence markers (غ، غائب، etc)
     * Returns the numeric value otherwise
     */
    public function parseScoreValue($score): ?float
    {
        // If already numeric
        if (is_numeric($score)) {
            $numericValue = (float) $score;
            // Negative scores are excluded from total (not added to sum)
            if ($numericValue < 0) {
                return null;
            }
            return $numericValue;
        }

        // If null or empty
        if ($score === null || $score === '') {
            return null;
        }

        // Convert to string and trim
        $score = trim((string) $score);
        
        // Check for absence markers - treat as 0
        if ($this->isAbsentMarker($score)) {
            return 0.0;
        }

        // Try to extract numeric value from string (e.g., "50 درجة" → 50 or "-4" → null)
        if (preg_match('/-?[\d٠-٩]+([.,][\d٠-٩]+)?/', $score, $matches)) {
            // Convert Arabic numerals to Western
            $num = strtr($matches[0], [
                '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                ',' => '.'
            ]);
            $numericValue = (float) $num;
            // Negative scores are excluded from total (not added to sum)
            if ($numericValue < 0) {
                return null;
            }
            return $numericValue;
        }

        // Can't parse - return null (will be skipped)
        return null;
    }

    /**
     * Parse a score value, handling various formats including absence markers
     * Returns null if the score should be skipped (like metadata fields)
     * Returns 0 for absence markers (غ، غائب، etc)
     * Returns the numeric value otherwise
     * Note: Negative scores return null (excluded from total)
     */
    public static function parseScore($score): ?float
    {
        // If already numeric
        if (is_numeric($score)) {
            $numericValue = (float) $score;
            // Negative scores are excluded from total (not added to sum)
            if ($numericValue < 0) {
                return null;
            }
            return $numericValue;
        }

        // If null or empty
        if ($score === null || $score === '') {
            return null;
        }

        // Convert to string and trim
        $score = trim((string) $score);
        
        // Check for absence markers (غ، غائب، غ.، -, etc) - treat as 0
        $absenceMarkers = [
            'غ', 'غ.', 'غائب', 'غائبة', 'غياب',
            '-', '--', '---',
            'absent', 'abs',
            'محروم', 'راسب', 'ر',
            '0', '٠',
        ];
        
        $normalizedScore = mb_strtolower($score);
        $normalizedScore = str_replace(['أ', 'إ', 'آ'], 'ا', $normalizedScore);
        
        foreach ($absenceMarkers as $marker) {
            if ($normalizedScore === mb_strtolower($marker)) {
                return 0.0; // Absent = 0 score
            }
        }

        // Try to extract numeric value from string (e.g., "50 درجة" → 50 or "-4" → null)
        if (preg_match('/-?[\d٠-٩]+([.,][\d٠-٩]+)?/', $score, $matches)) {
            // Convert Arabic numerals to Western
            $num = strtr($matches[0], [
                '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                ',' => '.'
            ]);
            $numericValue = (float) $num;
            // Negative scores are excluded from total (not added to sum)
            if ($numericValue < 0) {
                return null;
            }
            return $numericValue;
        }

        // Can't parse - return null (will be skipped)
        return null;
    }
}
