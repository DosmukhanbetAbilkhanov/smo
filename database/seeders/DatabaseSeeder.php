<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\Shop;
use App\Models\Spec;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $setup = config('setup');

        /**
         * ============================
         * 🧱 SEED UNITS
         * ============================
         */
        foreach ($setup['units'] as $unit) {
            Unit::create([
                'name_ru' => $unit['name']['ru'],
                'name_kz' => $unit['name']['kz'],
            ]);
        }

        /**
         * ============================
         * 🗂️ SEED CATEGORIES + CHILDREN
         * ============================
         */
        foreach ($setup['categories'] as $cat) {

            $parent = Category::create([
                'name_ru' => $cat['name']['ru'],
                'name_kz' => $cat['name']['kz'],
                'parent_id' => null,
                'slug' => Str::slug($cat['name']['ru']),
            ]);

            if (isset($cat['children'])) {
                foreach ($cat['children'] as $child) {
                    Category::create([
                        'name_ru' => $child['name']['ru'],
                        'name_kz' => $child['name']['kz'],
                        'parent_id' => $parent->id,
                        'slug' => Str::slug($child['name']['ru']),
                    ]);
                }
            }
        }

        /**
         * ============================
         * 👤 SEED USER TYPES
         * ============================
         */
        foreach ($setup['user_types'] as $type) {
            UserType::create([
                'name_ru' => $type['name']['ru'],
                'name_kz' => $type['name']['kz'],
            ]);
        }

        // 1️⃣ Города
        $cities = City::factory(4)->create();

        // 2️⃣ Пользователи: продавцы + покупатели
        $sellers = User::factory(5)->create(['type_id' => 4]); // Seller
        $retailCustomers = User::factory(5)->create(['type_id' => 2]); // Retail Customer
        $companyCustomers = User::factory(5)->create(['type_id' => 3]); // Company Customer
        $admins = User::factory(2)->create(['type_id' => 1]); // Admin

        // 3️⃣ Компании для продавцов
        $companies = $sellers->map(function ($seller) use ($cities) {
            return Company::factory()->create([
                'user_id' => $seller->id,
                'city_id' => $cities->random()->id,
            ]);
        });

        // 4️⃣ Магазины для каждой компании
        $shops = collect();
        foreach ($companies as $company) {
            $companyShops = Shop::factory(rand(1, 3))->create([
                'company_id' => $company->id,
                'city_id' => $company->city_id,
            ]);
            $shops = $shops->concat($companyShops);
        }

        // 5️⃣ Спецификации
        $specs = Spec::factory(20)->create();

        // 6️⃣ Номенклатуры с категориями и единицами
        $units = Unit::all();
        $categories = Category::whereNull('parent_id')->get(); // родительские категории
        $nomenclatures = collect();

        foreach ($categories as $category) {
            $children = $category->children;
            foreach ($children as $child) {
                $noms = Nomenclature::factory(rand(5, 10))->create([
                    'unit_id' => $units->random()->id,
                    'category_id' => $child->id,
                ]);
                $nomenclatures = $nomenclatures->concat($noms);
            }
        }

        // 7️⃣ Продукты для магазинов
        $products = collect();
        foreach ($shops as $shop) {
            $shopProducts = $nomenclatures->random(rand(5, 15))->map(function ($nom) use ($shop) {
                return Product::factory()->create([
                    'shop_id' => $shop->id,
                    'nomenclature_id' => $nom->id,
                    'name_ru' => $nom->name_ru,
                    'name_kz' => $nom->name_kz,
                    'price' => rand(500, 50000), // случайная цена
                    'quantity' => rand(1, 200), // случайный остаток
                ]);
            });
            $products = $products->concat($shopProducts);
        }

        // 8️⃣ ProductSpecs из категории номенклатуры
        foreach ($products as $product) {
            $categorySpecs = $product->nomenclature->category->specs;
            $numSpecs = min(rand(1, 5), $categorySpecs->count());
            $specsSample = $categorySpecs->random($numSpecs);

            foreach ($specsSample as $spec) {
                ProductSpec::factory()->create([
                    'product_id' => $product->id,
                    'spec_name' => $spec->name_ru,
                    'spec_value' => 'Значение '.rand(1, 100),
                ]);
            }
        }

        $this->command->info('✅ Database seeded with realistic marketplace data.');
    }
}
