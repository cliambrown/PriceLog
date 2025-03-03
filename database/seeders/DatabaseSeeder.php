<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Entry;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $liam = new User;
        $liam->name = 'Liam';
        $liam->email = env('LIAM_EMAIL');
        $liam->password = Hash::make(env('LIAM_PASSWORD'));
        $liam->save();
        
        $beccah = new User;
        $beccah->name = 'Beccah';
        $beccah->email = env('BECCAH_EMAIL');
        $beccah->password = Hash::make(env('BECCAH_PASSWORD'));
        $beccah->save();
        
        // $itemNames = [
        //     'Frozen Yogourt',
        //     'Lemons',
        //     'Blue Gatorade',
        //     'Russet potatoes',
        //     'Honey',
        //     'String Cheese',
        //     'Honeydew Melons',
        //     'Chocolate',
        //     "Trader Joe's Dark Chocolate Covered Honey Grahams with Sea Salt (227g)",
        //     'Dark Chocolate',
        // ];
        
        // $stores = [
        //     '3P',
        //     'Maxi',
        //     'Pharmaprix',
        //     'JT Market',
        //     'Jardin du Parc Fruterie',
        //     'Loco',
        //     'Metro',
        //     'Mégavrac Villeray',
        //     'Vrac en Folie (JT)',
        //     'PA Nature',
        //     'IGA St Zotique',
        //     'Super C',
        //     "Segal's",
        // ];
        
        // for ($i=0; $i<10; ++$i) {
        //     $item = new Item;
        //     $item->name = $itemNames[$i];
        //     $item->last_checked_at = fake()->dateTimeBetween('-1 month', 'now');
        //     $item->save();
            
        //     $entryCount = mt_rand(1, 6);
        //     if ($i == 9 || $i == 0) $entryCount = 6;
        //     for ($j=0; $j<$entryCount; ++$j) {
        //         $entry = new Entry;
        //         $entry->item_id = $item->id;
        //         $entry->location = $stores[array_rand($stores)];
        //         $entry->is_sale = fake()->boolean(20);
        //         $entry->price = fake()->randomFloat(2, 0.5, 30);
        //         $entry->seen_on = fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d');
        //         $entry->notes = fake()->boolean(20) ? fake()->sentence(5, true) : null;
        //         $entry->save();
        //     }
        // }
        
    }
}
