<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Vonage\Laravel\Facade\Vonage;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Search CAPI Users if they have not been created, does not handle pagination
        $response = Vonage::get('https://api.nexmo.com/v0.3/users');
        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responsePayload['_embedded']['users'] as $user) {
            if ($user['name'] = 'helpdesk_super_user') {
                $capiCode = $user['id'];
                break;
            }
            $capiCode = null;
        }

        if ($capiCode === null) {
            $response = Vonage::post('https://api.nexmo.com/v0.3/users', [
                'name' => 'helpdesk_super_user',
                'display_name' => 'Vonage Helpdesk Admin'
            ]);

            $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $capiCode = $responsePayload['id'];
        }

        User::factory()->create([
             'name' => 'Super Admin',
             'email' => 'admin@vonage.com',
             'password' => Hash::make('password'),
             'capi_user_id' => $capiCode
         ]);
    }
}
