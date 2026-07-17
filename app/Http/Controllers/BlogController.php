<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        
        $query = Post::published();
        
        if ($category && in_array($category, ['results', 'alternatives', 'capabilities', 'grades'])) {
            $query->where('category', $category);
        }
        
        $posts = $query->orderBy('published_at', 'desc')->paginate(9);
        
        $categoryTitle = match($category) {
            'results' => 'أخبار نتائج الامتحانات',
            'alternatives' => 'بدائل الشهادة الإعدادية',
            'capabilities' => 'اختبارات القدرات',
            'grades' => 'توزيع الدرجات',
            default => null
        };
        
        $pageTitle = $categoryTitle ? "{$categoryTitle} 2026" : 'مدونة نتيجتي التعليمية | آخر الأخبار والمستجدات';
        $pageDesc = $categoryTitle 
            ? "متابعة مستمرة لآخر مستجدات وأخبار {$categoryTitle} لعام 2026 لحظة بلحظة مع كافة الشروط والمواعيد الرسمية."
            : 'مدونة نتيجتي التعليمية - تغطية حصرية ومحدثة لحظة بلحظة لأخبار نتائج الامتحانات، وبدائل الثانوية العامة، واختبارات القدرات وتوزيع درجات المواد.';

        $meta = $this->seoService->generateMetaTags($pageTitle, $pageDesc);

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'المدونة', 'url' => route('blog.index')],
        ];
        
        if ($categoryTitle) {
            $breadcrumbs[] = ['name' => $categoryTitle, 'url' => ''];
        }

        $structuredData = $this->seoService->generateBreadcrumbSchema($breadcrumbs);

        return view('blog.index', compact('posts', 'meta', 'breadcrumbs', 'structuredData', 'category', 'categoryTitle'));
    }

    /**
     * Display the specified blog post.
     */
    public function show(Post $post)
    {
        // Ensure post is published
        if (!$post->is_published || !$post->published_at || $post->published_at->isFuture()) {
            abort(404);
        }

        $meta = $this->seoService->generateMetaTags(
            $post->seo_title ?? $post->title,
            $post->seo_description ?? $post->summary ?? Str::limit(strip_tags($post->content), 150),
            $post->image_path ? asset($post->image_path) : null
        );

        if ($post->seo_keywords) {
            $meta['keywords'] = $post->seo_keywords;
        }

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'المدونة', 'url' => route('blog.index')],
            ['name' => $post->getCategoryNameArAttribute(), 'url' => route('blog.index', ['category' => $post->category])],
            ['name' => $post->title, 'url' => ''],
        ];

        $structuredData = SchemaService::blogPostPage($post);

        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        return view('blog.show', compact('post', 'meta', 'breadcrumbs', 'structuredData', 'recentPosts'));
    }
}
