<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(MobileRegisterSeeder::class);
        $this->call(EmailVerificationSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(AddedLanguageControlKeyInSettingsTable::class);
        $this->call(AppLinkSeeder::class);
        $this->call(AddedSliderKeys::class);
        $this->call(ScriptSettingSeeder::class);
        $this->call(VideoSettingsSeeder::class);
        $this->call(AdminDemoLoginSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AddedMaxsizekeysInSettings::class);
        $this->call(SubProfileSeeder::class);
        $this->call(AddedStripeKeyInSettings::class);
        $this->call(AddedViewCountInSettingsTable::class);
        $this->call(AddedRedeemOptionInSettings::class);
        $this->call(AddSocialLinksSeeder::class);
        $this->call(TokenExpirySeeder::class);
        $this->call(PageDemoSeeder::class);
        $this->call(ModeratorSeeder::class);
    }
}
