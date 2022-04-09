<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Tag::count()) {
            Tag::truncate();
        }

        $tags = [
            'عمومی',
            'سریال و فیلم‌های سینمایی',
            'گیم',
            'ورزشی',
            'کارتون',
            'طنز',
            'آموزشی',
            'تفریحی',
            'فیلم',
            'مذهبی',
            'موسیقی',
            'خبری',
            'سیاسی',
            'علم و تکنولوژی',
            'حوادث',
            'گردشگری',
            'حیوانات',
            'متفرقه',
            'تبلیغات',
            'هنری',
            'بانوان',
            'سلامت',
            'آشپزی',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'title' => $tag,
            ]);
        }

        $this->command->info('Tags are created successfully!');
    }
}
