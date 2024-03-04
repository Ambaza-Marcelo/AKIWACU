<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'ambaza@gmail.com')->first();
        if (is_null($user)) {
            $user = new User();
            $user->name = "Ambaza Marcellin";
            $user->email = "ambaza@gmail.com";
            $user->password = Hash::make('MUSUMBA');
            $user->save();
        }
    }
}
