<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Policies\AdCampaignPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdCampaignPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected AdCampaignPolicy $policy;
    protected User $admin;
    protected User $advertiser;
    protected User $otherAdvertiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new AdCampaignPolicy();

        // Создаём роли
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('advertiser', 'web');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->advertiser = User::factory()->create();
        $this->advertiser->assignRole('advertiser');

        $this->otherAdvertiser = User::factory()->create();
        $this->otherAdvertiser->assignRole('advertiser');
    }

    /** @test */
    public function admin_can_view_any_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser);

        $this->assertTrue($this->policy->view($this->admin, $campaign));
    }

    /** @test */
    public function advertiser_can_view_own_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser);

        $this->assertTrue($this->policy->view($this->advertiser, $campaign));
    }

    /** @test */
    public function advertiser_cannot_view_others_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser);

        $this->assertFalse($this->policy->view($this->otherAdvertiser, $campaign));
    }

    /** @test */
    public function cannot_update_active_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser, 'active');

        $this->assertFalse($this->policy->update($this->advertiser, $campaign));
    }

    /** @test */
    public function can_update_pending_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser, 'pending');

        $this->assertTrue($this->policy->update($this->advertiser, $campaign));
    }

    /** @test */
    public function cannot_delete_active_campaign()
    {
        $campaign = $this->createCampaign($this->advertiser, 'active');

        $this->assertFalse($this->policy->delete($this->advertiser, $campaign));
    }

    /** @test */
    public function can_pause_only_active_campaign()
    {
        $active = $this->createCampaign($this->advertiser, 'active');
        $paused = $this->createCampaign($this->advertiser, 'paused');

        $this->assertTrue($this->policy->pause($this->advertiser, $active));
        $this->assertFalse($this->policy->pause($this->advertiser, $paused));
    }

    /** @test */
    public function can_resume_only_paused_campaign()
    {
        $active = $this->createCampaign($this->advertiser, 'active');
        $paused = $this->createCampaign($this->advertiser, 'paused');

        $this->assertFalse($this->policy->resume($this->advertiser, $active));
        $this->assertTrue($this->policy->resume($this->advertiser, $paused));
    }

    private function createCampaign(User $owner, string $status = 'pending'): AdCampaign
    {
        $placement = AdPlacement::create([
            'code' => 'test_' . uniqid(),
            'name' => 'Test Placement',
            'is_active' => true,
        ]);

        return AdCampaign::create([
            'advertiser_id' => $owner->id,
            'placement_id' => $placement->id,
            'name' => 'Test Campaign',
            'budget' => 1000,
            'type' => 'cpm',
            'status' => $status,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);
    }
}
