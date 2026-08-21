<?php

namespace Tests\Feature;

use App\Livewire\Site\Club\Register;
use App\Models\ClubMember;
use App\Models\NgnCompaign;
use App\Models\NgnCompaignReferral;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class ClubReferralLivewireJoinTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('ngn_campaigns')
            || ! Schema::hasTable('ngn_campaign_referrals')
            || ! Schema::hasTable('club_members')) {
            $this->markTestSkipped('Club referral tables are not present.');
        }

        Mail::fake();
    }

    public function test_subscribe_query_accepts_an_active_referral_and_marks_it_validated_on_join(): void
    {
        $referrer = $this->makeClubMember('Riley Referrer', 'riley-'.$this->uniq().'@example.test');
        $campaign = NgnCompaign::query()->create([
            'name' => 'Referral test '.$this->uniq(),
            'description' => 'Livewire join test',
            'status' => 'Active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ]);
        $code = (string) random_int(100000, 999999);
        $referral = NgnCompaignReferral::query()->create([
            'ngn_campaign_id' => $campaign->id,
            'referrer_club_member_id' => $referrer->id,
            'referred_full_name' => 'Alex Friend',
            'referred_phone' => '07123400999',
            'referred_reg_number' => 'AB12CDE',
            'referral_code' => $code,
            'validated' => false,
        ]);

        $email = 'alex-'.$this->uniq().'@example.test';
        $phone = '07123'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Livewire::withQueryParams([
            'ref' => $code,
            'id' => (string) $referrer->id,
        ])->test(Register::class)
            ->assertSet('referralAccepted', true)
            ->assertSet('referralCode', $code)
            ->set('full_name', 'Alex Friend')
            ->set('email', $email)
            ->set('phone', $phone)
            ->set('tc_agreed', true)
            ->call('joinClub')
            ->assertHasNoErrors();

        $this->assertTrue((bool) $referral->fresh()->validated);
        $this->assertTrue(ClubMember::query()->where('email', $email)->exists());
    }

    public function test_active_campaign_with_elapsed_dates_still_accepts_the_join_link(): void
    {
        $referrer = $this->makeClubMember('Riley Referrer', 'riley-'.$this->uniq().'@example.test');
        $campaign = NgnCompaign::query()->create([
            'name' => 'Referral elapsed '.$this->uniq(),
            'description' => 'Dates ended, status still Active',
            'status' => 'Active',
            'start_date' => now()->subYears(2),
            'end_date' => now()->subMonths(8),
        ]);
        $code = (string) random_int(100000, 999999);
        NgnCompaignReferral::query()->create([
            'ngn_campaign_id' => $campaign->id,
            'referrer_club_member_id' => $referrer->id,
            'referred_full_name' => 'Alex Friend',
            'referred_phone' => '07123400888',
            'referred_reg_number' => 'AB12CDE',
            'referral_code' => $code,
            'validated' => false,
        ]);

        Livewire::withQueryParams([
            'ref' => $code,
            'id' => (string) $referrer->id,
        ])->test(Register::class)
            ->assertSet('referralAccepted', true)
            ->assertSet('referralCode', $code);
    }

    public function test_invalid_referral_query_does_not_block_a_normal_join(): void
    {
        $email = 'join-'.$this->uniq().'@example.test';
        $phone = '07124'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Livewire::withQueryParams([
            'ref' => '000000',
            'id' => '999999',
        ])->test(Register::class)
            ->assertSet('referralAccepted', false)
            ->assertSet('referralCode', '')
            ->set('full_name', 'Pat Solo')
            ->set('email', $email)
            ->set('phone', $phone)
            ->set('tc_agreed', true)
            ->call('joinClub')
            ->assertHasNoErrors();

        $this->assertTrue(ClubMember::query()->where('email', $email)->exists());
    }

    private function makeClubMember(string $name, string $email): ClubMember
    {
        return ClubMember::query()->create([
            'full_name' => $name,
            'email' => $email,
            'phone' => '07125'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'tc_agreed' => true,
            'is_active' => true,
            'passkey' => '123456',
        ]);
    }

    private function uniq(): string
    {
        return substr(str_replace('.', '', uniqid('', true)), -10);
    }
}
