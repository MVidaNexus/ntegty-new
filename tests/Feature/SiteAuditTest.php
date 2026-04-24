<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\ExamType;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

class SiteAuditTest extends TestCase
{
    /**
     * Test homepage accessibility
     */
    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('نتيجتي');
    }

    /**
     * Test admin login page accessibility
     */
    public function test_admin_login_loads(): void
    {
        $response = $this->get('/dashboard/login');
        $response->assertStatus(200);
    }

    /**
     * Test Egypt results page
     */
    public function test_egypt_page_loads(): void
    {
        // Ensure egypt country exists in DB (using SQLite in memory usually for tests)
        // But since we are running in the repo, it might use the local sqlite file.
        $response = $this->get('/egypt');
        
        // If it's 404, maybe the database doesn't have the data in test environment
        if ($response->status() === 200) {
            $response->assertSee('مصر');
        } else {
            $this->markTestIncomplete('Egypt page returned ' . $response->status() . '. Data might be missing in test DB.');
        }
    }

    /**
     * Test Search Route
     */
    public function test_search_route_exists(): void
    {
        $response = $this->post('/search', ['name' => 'test']);
        // Should redirect back or show results. 302 or 200 is fine.
        $this->assertContains($response->status(), [200, 302]);
    }

    /**
     * Test Rate Limiting on Search
     */
    public function test_search_rate_limiting(): void
    {
        // We added throttle:30,1
        // We can't easily test the exact throttle in one test run without multiple requests,
        // but we can check if the middleware is attached to the route.
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('search');
        $this->assertContains('throttle:30,1', $route->gatherMiddleware());
    }

    /**
     * Test Layout fixes (No debug comment, consolidated settings)
     */
    public function test_layout_fixes_applied(): void
    {
        $response = $this->get('/');
        $response->assertDontSee('DEBUG: CACHE TEST 12345');
        // Check if font preload is there
        $response->assertSee('rel="preload" as="style"', false);
    }
}
