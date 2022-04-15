<?php

namespace Database\Seeders;

use App\Models\Playlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Playlist::count()) {
            Playlist::truncate();
        }

        $playlists = [
            'first playlist',
            'second playlist',
        ];

        foreach ($playlists as $playlist) {
            Playlist::create([
                'user_id' => 11,
                'title' => $playlist
            ]);
        }

        $this->command->info('Playlists are inserted!');
    }
}
