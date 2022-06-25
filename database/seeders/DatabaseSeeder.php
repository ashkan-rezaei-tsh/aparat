<?php

namespace Database\Seeders;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Schema::disableForeignKeyConstraints();
        if (User::count()) {
            User::truncate();
        }
        User::factory(10)->create();
        User::factory(1)->admin()->create();

        $this->call(CategorySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(PlaylistSeeder::class);

        Schema::enableForeignKeyConstraints();
		
		Artisan::call('aparat:clear');
		$this->command->info('Cleared all aparat temporary files successfully.');
    }
}
