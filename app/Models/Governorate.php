<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name_ar',
        'name_en',
        'slug',
        'logo_path',
        'result_pdf_path',
        'is_declared',
        // SEO Fields
        'seo_title',
        'seo_description',
        'seo_keywords',
        // Content Box Fields
        'show_content_section',
        'content_title',
        'content_intro',
        'content_body',
        // Result Service Settings
        'result_service_type',
        'embed_code',
        'iframe_url',
        'iframe_width',
        'iframe_height',
        'iframe_position',
        'iframe_scrolling',
        'iframe_border',
        'iframe_crop_enabled',
        'iframe_crop_top',
        'iframe_crop_left',
        'iframe_crop_right',
        'iframe_crop_bottom',
        'iframe_zoom',
        'pdf_file_path',
    ];

    protected $casts = [
        'is_declared' => 'boolean',
        'show_content_section' => 'boolean',
        'iframe_scrolling' => 'boolean',
        'iframe_border' => 'boolean',
        'iframe_crop_enabled' => 'boolean',
        'iframe_crop_top' => 'integer',
        'iframe_crop_left' => 'integer',
        'iframe_crop_right' => 'integer',
        'iframe_crop_bottom' => 'integer',
        'iframe_zoom' => 'float',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Check if governorate has result PDF file
     */
    public function hasResultPdf(): bool
    {
        return !empty($this->result_pdf_path);
    }

    /**
     * Get result PDF URL
     */
    public function getResultPdfUrl(): ?string
    {
        if (!$this->hasResultPdf()) {
            return null;
        }
        return asset('storage/' . $this->result_pdf_path);
    }

    /**
     * Get result status label
     */
    public function getResultStatusLabel(): string
    {
        if ($this->is_declared) {
            return $this->hasResultPdf() ? 'متاحة للتحميل' : 'معتمدة';
        }
        return 'قريباً';
    }

    /**
     * Get result status color
     */
    public function getResultStatusColor(): string
    {
        if ($this->is_declared && $this->hasResultPdf()) {
            return 'success'; // green
        }
        if ($this->is_declared) {
            return 'warning'; // yellow - معتمدة لكن بدون ملف
        }
        return 'gray'; // قريباً
    }

    /**
     * Check if governorate uses embed/iframe
     */
    public function usesEmbed(): bool
    {
        return $this->result_service_type === 'embed' && !empty($this->embed_code);
    }

    /**
     * Check if governorate uses PDF
     */
    public function usesPdf(): bool
    {
        return $this->result_service_type === 'pdf' && !empty($this->pdf_file_path);
    }

    /**
     * Check if governorate uses search (default)
     */
    public function usesSearch(): bool
    {
        return $this->result_service_type === 'search' || empty($this->result_service_type);
    }

    /**
     * Get the effective result service type
     */
    public function getEffectiveServiceType(): string
    {
        return $this->result_service_type ?? 'search';
    }

    /**
     * Get formatted content body
     * Converts pipe-separated text to tables
     * Preserves existing HTML (headers, paragraphs, etc.)
     * Decodes HTML entities if content was double-encoded
     */
    public function getFormattedContentBody(): string
    {
        $content = $this->content_body ?? '';
        
        if (empty($content)) {
            return '';
        }
        
        // Decode HTML entities if content contains encoded tags (from RichEditor)
        // This handles cases like &lt;h2&gt; which should be <h2>
        if (str_contains($content, '&lt;') || str_contains($content, '&gt;')) {
            $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Remove wrapping <p> tags that RichEditor adds
        $content = preg_replace('/^<p>\s*/', '', $content);
        $content = preg_replace('/\s*<\/p>$/', '', $content);
        
        // CRITICAL: Remove broken/incomplete tables that can break page layout
        // A table is broken if it has <table but no </table>
        $tableOpenCount = preg_match_all('/<table\b/i', $content);
        $tableCloseCount = preg_match_all('/<\/table>/i', $content);
        if ($tableOpenCount > $tableCloseCount) {
            // Remove incomplete tables - find last <table and remove everything from there
            $content = preg_replace('/<table\b[^>]*>(?!.*<\/table>).*$/is', '', $content);
        }
        
        // Remove <br> inside lists (between <ol>/<ul> and <li>, and between <li> elements)
        $content = preg_replace('/<ol>\s*<br\s*\/?>\s*/i', '<ol>', $content);
        $content = preg_replace('/<ul>\s*<br\s*\/?>\s*/i', '<ul>', $content);
        // Handle: </li><br>  <li> or </li><br><li>
        $content = preg_replace('/<\/li>\s*<br\s*\/?>\s*<li>/i', '</li><li>', $content);
        // Handle: </li><br>  </ol> or </li><br></ol>
        $content = preg_replace('/<\/li>\s*<br\s*\/?>\s*<\/ol>/i', '</li></ol>', $content);
        $content = preg_replace('/<\/li>\s*<br\s*\/?>\s*<\/ul>/i', '</li></ul>', $content);
        // Remove any <br> immediately before <li>
        $content = preg_replace('/<br\s*\/?>\s*<li>/i', '<li>', $content);
        // Remove whitespace and <br> after </li>
        $content = preg_replace('/<\/li>\s*<br\s*\/?>/i', '</li>', $content);
        
        // Remove <br> inside tables (between rows and cells)
        $content = preg_replace('/<table([^>]*)>\s*<br\s*\/?>\s*/i', '<table$1>', $content);
        $content = preg_replace('/<thead>\s*<br\s*\/?>\s*/i', '<thead>', $content);
        $content = preg_replace('/<tbody>\s*<br\s*\/?>\s*/i', '<tbody>', $content);
        $content = preg_replace('/<tr([^>]*)>\s*<br\s*\/?>\s*/i', '<tr$1>', $content);
        $content = preg_replace('/<\/tr>\s*<br\s*\/?>\s*/i', '</tr>', $content);
        $content = preg_replace('/<\/thead>\s*<br\s*\/?>\s*/i', '</thead>', $content);
        $content = preg_replace('/<\/tbody>\s*<br\s*\/?>\s*/i', '</tbody>', $content);
        $content = preg_replace('/<\/table>\s*<br\s*\/?>\s*/i', '</table>', $content);
        
        // Replace <br> followed by HTML tags with just the tag (remove extra breaks)
        $content = preg_replace('/<br\s*\/?>\s*(<h[1-6])/i', '$1', $content);
        $content = preg_replace('/<br\s*\/?>\s*(<p)/i', '$1', $content);
        $content = preg_replace('/<br\s*\/?>\s*(<ul)/i', '$1', $content);
        $content = preg_replace('/<br\s*\/?>\s*(<ol)/i', '$1', $content);
        $content = preg_replace('/<br\s*\/?>\s*(<table)/i', '$1', $content);
        
        // Replace HTML tags followed by <br> with just the tag
        $content = preg_replace('/(<\/h[1-6]>)\s*<br\s*\/?>/i', '$1', $content);
        $content = preg_replace('/(<\/p>)\s*<br\s*\/?>/i', '$1', $content);
        $content = preg_replace('/(<\/ul>)\s*<br\s*\/?>/i', '$1', $content);
        $content = preg_replace('/(<\/ol>)\s*<br\s*\/?>/i', '$1', $content);
        $content = preg_replace('/(<\/table>)\s*<br\s*\/?>/i', '$1', $content);
        
        // Remove multiple consecutive <br> tags
        $content = preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br>', $content);
        
        // Remove &nbsp; 
        $content = str_replace('&nbsp;', ' ', $content);
        
        // Check if content has pipe-separated text that should be a table
        if (str_contains(strip_tags($content), '|')) {
            $content = $this->convertPipeTextToTable($content);
        }
        
        return trim($content);
    }

    /**
     * Convert pipe-separated text blocks to HTML tables
     */
    protected function convertPipeTextToTable(string $content): string
    {
        // Check if there are pipe patterns
        if (!str_contains(strip_tags($content), '|')) {
            return $content;
        }
        
        // Find blocks that contain pipe-separated lines
        $pattern = '/(<p[^>]*>)((?:[^<]*\|[^<]*)+)(<\/p>)/';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            // No paragraph-wrapped pipe content, check for plain text
            $plainText = strip_tags($content);
            if (str_contains($plainText, '|')) {
                $lines = preg_split('/\r?\n/', $plainText);
                $lines = array_map('trim', $lines);
                $pipeLines = array_filter($lines, fn($line) => str_contains($line, '|') && !empty($line));
                
                if (count($pipeLines) >= 2) {
                    $table = $this->buildTableHtml(array_values($pipeLines));
                    
                    foreach ($pipeLines as $line) {
                        $escapedLine = preg_quote($line, '/');
                        $content = preg_replace('/<p[^>]*>\s*' . $escapedLine . '\s*<\/p>/', '', $content, 1);
                    }
                    
                    $content .= $table;
                }
            }
            return $content;
        }
        
        $pipeLines = [];
        $paragraphsToRemove = [];
        
        foreach ($matches as $match) {
            $pContent = html_entity_decode(strip_tags($match[2]));
            if (str_contains($pContent, '|')) {
                $pipeLines[] = trim($pContent);
                $paragraphsToRemove[] = $match[0];
            }
        }
        
        if (count($pipeLines) >= 2) {
            $table = $this->buildTableHtml($pipeLines);
            
            foreach ($paragraphsToRemove as $para) {
                $content = str_replace($para, '', $content);
            }
            
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
            $cells = array_filter($cells, fn($c) => $c !== '');
            
            if (empty($cells)) continue;
            
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
}
