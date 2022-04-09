<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Category::count()) {
            Category::truncate();
        }

        $categories = [
            'عمومی' => ['icon' => '', 'banner' => ''],
            'سریال و فیلم‌های سینمایی' => ['icon' => '', 'banner' => ''],
            'گیم' => ['icon' => '', 'banner' => ''],
            'ورزشی' => ['icon' => '', 'banner' => ''],
            'کارتون' => ['icon' => '', 'banner' => ''],
            'طنز' => ['icon' => '', 'banner' => ''],
            'آموزشی' => ['icon' => '', 'banner' => ''],
            'تفریحی' => ['icon' => '', 'banner' => ''],
            'فیلم' => ['icon' => '', 'banner' => ''],
            'مذهبی' => ['icon' => '', 'banner' => ''],
            'موسیقی' => ['icon' => '', 'banner' => ''],
            'خبری' => ['icon' => '', 'banner' => ''],
            'سیاسی' => ['icon' => '', 'banner' => ''],
            'علم و تکنولوژی' => ['icon' => '', 'banner' => ''],
            'حوادث' => ['icon' => '', 'banner' => ''],
            'گردشگری' => ['icon' => '', 'banner' => ''],
            'حیوانات' => ['icon' => '', 'banner' => ''],
            'متفرقه' => ['icon' => '', 'banner' => ''],
            'تبلیغات' => ['icon' => '', 'banner' => ''],
            'هنری' => ['icon' => '', 'banner' => ''],
            'بانوان' => ['icon' => '', 'banner' => ''],
            'سلامت' => ['icon' => '', 'banner' => ''],
            'آشپزی' => ['icon' => '', 'banner' => ''],
        ];

        foreach ($categories as $categoryName => $options) {
            Category::create([
                'title' => $categoryName,
                'icon' => $options['icon'],
                'banner' => $options['banner'],
            ]);
        }

        $this->command->info('Categories added successfully!');
    }
}