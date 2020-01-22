<?php

use Illuminate\Database\Seeder;

use App\Settings;
use App\User;

class SampleSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();


        $settings = Settings::create();

        $settings->ceo_id = User::get()->random()->id;
        $settings->hr_id = User::get()->random()->id;
        $settings->od_id = User::get()->random()->id;
        $settings->save();
    }
}
