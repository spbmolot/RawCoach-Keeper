<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function home_page_loads_successfully()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }

    /** @test */
    public function about_page_loads_successfully()
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);
    }

    /** @test */
    public function contact_page_loads_successfully()
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
    }

    /** @test */
    public function privacy_page_loads_successfully()
    {
        $response = $this->get(route('privacy'));
        $response->assertStatus(200);
    }

    /** @test */
    public function terms_page_loads_successfully()
    {
        $response = $this->get(route('terms'));
        $response->assertStatus(200);
    }

    /** @test */
    public function offer_page_loads_successfully()
    {
        $response = $this->get(route('offer'));
        $response->assertStatus(200);
    }

    /** @test */
    public function plans_index_page_loads_successfully()
    {
        $response = $this->get(route('plans.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function plans_compare_page_loads_successfully()
    {
        $response = $this->get(route('plans.compare'));
        $response->assertStatus(200);
    }

    /** @test */
    public function demo_page_loads_successfully()
    {
        $response = $this->get(route('demo'));
        $response->assertStatus(200);
    }

    /** @test */
    public function sitemap_loads_successfully()
    {
        $response = $this->get(route('sitemap'));
        $response->assertStatus(200);
    }
}
