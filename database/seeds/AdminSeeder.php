<?php
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::where('username', 'superadmin')->first();

        if (is_null($admin)) {
            $admin           = new Admin();
            $admin->name     = "Ambaza Marcellin";
            $admin->email    = "marcellin@gmail.com";
            $admin->username = "superadmin";
            $admin->password = Hash::make('ambazamarcellin@2001');
            $admin->save();
        }
    }
}

?>
