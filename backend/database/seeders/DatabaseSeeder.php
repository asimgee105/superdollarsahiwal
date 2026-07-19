<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── STEP 1: Clean all tables ──────────────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'permissions','roles','role_has_permissions','model_has_roles',
            'model_has_permissions','users','user_profiles','addresses',
            'brands','categories','collections','homepage_layouts','homepage_sections',
            'sizes','colors','product_labels','products','product_variants',
            'product_media','product_category','product_collection',
            'product_relationships','product_reviews','orders','order_items',
            'order_transactions','order_timeline','coupons','posts',
            'post_categories','post_category_pivot','faqs','testimonials',
            'lookbooks','warehouses','inventory_items','return_requests','settings',
        ];
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $passwordHash = Hash::make('password');

        // ─── STEP 2: Roles & Permissions ──────────────────────────────────────
        $permissions = [
            ['name' => 'manage-users',        'guard_name' => 'web'],
            ['name' => 'view-dashboard',      'guard_name' => 'web'],
            ['name' => 'configure-settings',  'guard_name' => 'web'],
        ];
        DB::table('permissions')->insert($permissions);

        $roles = [
            ['name' => 'Super Admin',   'guard_name' => 'web'],
            ['name' => 'Administrator', 'guard_name' => 'web'],
            ['name' => 'Manager',       'guard_name' => 'web'],
            ['name' => 'Support',       'guard_name' => 'web'],
            ['name' => 'Customer',      'guard_name' => 'web'],
        ];
        DB::table('roles')->insert($roles);

        $superAdminRoleId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        $adminRoleId      = DB::table('roles')->where('name', 'Administrator')->value('id');
        $managerRoleId    = DB::table('roles')->where('name', 'Manager')->value('id');
        $customerRoleId   = DB::table('roles')->where('name', 'Customer')->value('id');

        $allPermissionIds = DB::table('permissions')->pluck('id')->toArray();
        foreach ($allPermissionIds as $permId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permId,
                'role_id'       => $superAdminRoleId,
            ]);
        }

        // ─── STEP 3: Admin Users ───────────────────────────────────────────────
        $superAdminId = DB::table('users')->insertGetId([
            'name'              => 'Super Admin',
            'email'             => 'admin@aura.com',
            'password'          => $passwordHash,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        DB::table('model_has_roles')->insert(['role_id' => $superAdminRoleId, 'model_type' => 'App\Models\User', 'model_id' => $superAdminId]);
        DB::table('user_profiles')->insert(['user_id' => $superAdminId, 'phone' => '03001234567', 'created_at' => now(), 'updated_at' => now()]);

        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin User', 'email' => 'admin1@example.com',
            'password' => $passwordHash, 'email_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert(['role_id' => $adminRoleId, 'model_type' => 'App\Models\User', 'model_id' => $adminId]);
        DB::table('user_profiles')->insert(['user_id' => $adminId, 'phone' => '03010000001', 'created_at' => now(), 'updated_at' => now()]);

        // ─── STEP 4: 5 Customers ──────────────────────────────────────────────
        $customerIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $id = DB::table('users')->insertGetId([
                'name' => "Customer $i", 'email' => "customer$i@example.com",
                'password' => $passwordHash, 'email_verified_at' => now(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $customerIds[] = $id;
            DB::table('model_has_roles')->insert(['role_id' => $customerRoleId, 'model_type' => 'App\Models\User', 'model_id' => $id]);
            DB::table('user_profiles')->insert(['user_id' => $id, 'phone' => '0333' . str_pad($i, 7, '0', STR_PAD_LEFT), 'gender' => ($i % 2 === 0 ? 'male' : 'female'), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('addresses')->insert([
                'user_id' => $id, 'name' => 'Home', 'type' => 'shipping',
                'address_line_1' => "$i Main Blvd", 'city' => 'Lahore',
                'state' => 'Punjab', 'postal_code' => '54000', 'country' => 'Pakistan',
                'phone' => '0333' . str_pad($i, 7, '0', STR_PAD_LEFT), 'is_default' => true,
            ]);
        }

        // ─── STEP 5: Brands ───────────────────────────────────────────────────
        $brandIds = [];
        $brandNames = ['AURA Premium', 'Vibe Wear', 'Denim Co', 'Kids Zone', 'Beauty Lab', 'GenZ Street'];
        foreach ($brandNames as $bn) {
            $brandIds[] = DB::table('brands')->insertGetId([
                'name' => $bn, 'slug' => Str::slug($bn),
                'description' => "Premium brand: $bn.", 'is_active' => true,
                'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ─── STEP 6: Sizes & Colors ───────────────────────────────────────────
        $sizeIds = [];
        foreach (['XS','S','M','L','XL','XXL'] as $sz) {
            $sizeIds[] = DB::table('sizes')->insertGetId(['name' => $sz, 'slug' => Str::slug($sz), 'type' => 'clothing', 'created_at' => now(), 'updated_at' => now()]);
        }
        $colorIds = [];
        foreach ([['Royal Black','#000000'],['Cream White','#ffffff'],['Classic Navy','#000080'],['Cherry Red','#dc143c'],['Forest Green','#228b22']] as [$cn,$ch]) {
            $colorIds[] = DB::table('colors')->insertGetId(['name' => $cn, 'hex_code' => $ch, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ─── STEP 7: Product Labels ───────────────────────────────────────────
        $labelNew  = DB::table('product_labels')->insertGetId(['name' => 'New',        'bg_color' => '#10b981', 'text_color' => '#ffffff', 'is_active' => true, 'created_at' => now()]);
        $labelHot  = DB::table('product_labels')->insertGetId(['name' => 'Hot',        'bg_color' => '#ff3f6c', 'text_color' => '#ffffff', 'is_active' => true, 'created_at' => now()]);
        $labelSale = DB::table('product_labels')->insertGetId(['name' => 'Sale',       'bg_color' => '#f59e0b', 'text_color' => '#ffffff', 'is_active' => true, 'created_at' => now()]);
        $labelBest = DB::table('product_labels')->insertGetId(['name' => 'Best Seller','bg_color' => '#6366f1', 'text_color' => '#ffffff', 'is_active' => true, 'created_at' => now()]);

        // ─── STEP 8: Collections ─────────────────────────────────────────────
        $collectionId = DB::table('collections')->insertGetId([
            'name' => 'New Arrivals', 'slug' => 'new-arrivals',
            'description' => 'Latest styles just dropped.', 'is_active' => true,
            'is_featured' => true, 'created_at' => now(),
        ]);
        $collectionSummer = DB::table('collections')->insertGetId([
            'name' => 'Summer 2026', 'slug' => 'summer-2026',
            'description' => 'Hot summer collection.', 'is_active' => true,
            'is_featured' => true, 'season' => 'summer', 'created_at' => now(),
        ]);

        // ─── STEP 9: Warehouses ───────────────────────────────────────────────
        $whIds = [];
        foreach ([['Karachi Hub','KHI-01','S.I.T.E Karachi'],['Lahore Central','LHR-02','Gulberg Lahore']] as [$wn,$wc,$wa]) {
            $whIds[] = DB::table('warehouses')->insertGetId(['name' => $wn, 'code' => $wc, 'address' => $wa, 'is_active' => true, 'created_at' => now()]);
        }

        // ─── STEP 10: 10 CATEGORIES with 2 products each ─────────────────────
        // Category structure: 10 main categories
        $categoriesData = [
            [
                'name' => 'Men',
                'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 60% OFF',
                'products' => [
                    ['title' => 'AURA Premium Denim Jacket', 'price' => 4999, 'sale' => 3499, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'High quality premium denim jacket, hand-crafted with modern streetwear aesthetic.'],
                    ['title' => 'AURA Smart Fit Oxford Shirt', 'price' => 2999, 'sale' => 1999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Classic smart fit oxford shirt. Breathable cotton blend ideal for formal wear.'],
                ],
            ],
            [
                'name' => 'Women',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 70% OFF',
                'products' => [
                    ['title' => 'AURA Linen Kurta Set', 'price' => 5999, 'sale' => 3999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Premium 2-piece linen kurta set with hand-woven embroidery.'],
                    ['title' => 'AURA Summer Maxi Dress', 'price' => 3499, 'sale' => 2299, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Flowy cotton summer maxi dress with floral patterns.'],
                ],
            ],
            [
                'name' => 'Kids',
                'image' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 50% OFF',
                'products' => [
                    ['title' => 'AURA Boys Casual Polo Shirt', 'price' => 1499, 'sale' => 999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Super soft cotton polo shirt for boys. Clean collar and button setup.'],
                    ['title' => 'AURA Girls Floral Cotton Dress', 'price' => 1999, 'sale' => 1299, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1607990283143-e81e7a2c93ab?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Aesthetic girls floral dress. Soft organic cotton for gentle skin.'],
                ],
            ],
            [
                'name' => 'Home & Living',
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 55% OFF',
                'products' => [
                    ['title' => 'AURA Organic Cotton Bedsheet Set', 'price' => 7999, 'sale' => 4999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=600&auto=format&fit=crop',
                     'desc' => '400 thread count organic cotton king size bedsheet set.'],
                    ['title' => 'AURA Velvet Cushion Cover Set', 'price' => 1299, 'sale' => 799, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Artisanal velvet cushion cover set. Elevates living room style instantly.'],
                ],
            ],
            [
                'name' => 'Beauty',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 40% OFF',
                'products' => [
                    ['title' => 'AURA Organic Lip & Cheek Tint', 'price' => 999, 'sale' => 699, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Clean organic formula lip and cheek tint. Natural rosy flush finish.'],
                    ['title' => 'AURA Citrus Infused Body Mist', 'price' => 1499, 'sale' => 999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Refreshing citrus infused daily body mist. Fresh all day.'],
                ],
            ],
            [
                'name' => 'GenZ',
                'image' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 65% OFF',
                'products' => [
                    ['title' => 'AURA Oversized Graphic Hoodie', 'price' => 3999, 'sale' => 2499, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Retro aesthetic oversized graphic hoodie. Brushed fleece inside.'],
                    ['title' => 'AURA Baggy Retro Utility Jeans', 'price' => 4500, 'sale' => 2999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'High quality retro baggy utility jeans. Multi-pocket dark indigo wash.'],
                ],
            ],
            [
                'name' => 'Footwear',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 45% OFF',
                'products' => [
                    ['title' => 'AURA Classic White Sneakers', 'price' => 5999, 'sale' => 3999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Timeless classic white leather sneakers. Comfortable all-day wear.'],
                    ['title' => 'AURA Women Block Heel Sandals', 'price' => 3499, 'sale' => 1999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Stylish block heel sandals for women. Perfect for summer evenings.'],
                ],
            ],
            [
                'name' => 'Sports',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 50% OFF',
                'products' => [
                    ['title' => 'AURA Dry-Fit Performance Tee', 'price' => 1999, 'sale' => 1299, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'High performance dry-fit training tee. Moisture wicking technology.'],
                    ['title' => 'AURA Yoga Flex Leggings', 'price' => 2999, 'sale' => 1799, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Four-way stretch yoga leggings. High waist compression fit.'],
                ],
            ],
            [
                'name' => 'Accessories',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 60% OFF',
                'products' => [
                    ['title' => 'AURA Premium Leather Tote Bag', 'price' => 8999, 'sale' => 5499, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Premium genuine leather structured tote bag. Spacious interior.'],
                    ['title' => 'AURA Aviator Sunglasses', 'price' => 2499, 'sale' => 1499, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Classic aviator sunglasses with UV400 protection. Gold metal frame.'],
                ],
            ],
            [
                'name' => 'Ethnic Wear',
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=600&auto=format&fit=crop',
                'discount' => 'UP TO 55% OFF',
                'products' => [
                    ['title' => 'AURA Embroidered Sherwani Set', 'price' => 18999, 'sale' => 13999, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Luxurious hand-embroidered sherwani set for weddings and festivals.'],
                    ['title' => 'AURA Printed Lawn Suit', 'price' => 3999, 'sale' => 2499, 'label' => null,
                     'img' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=600&auto=format&fit=crop',
                     'desc' => 'Beautiful 3-piece printed lawn suit. Summer-ready breathable fabric.'],
                ],
            ],
        ];

        // Insert categories and products
        $allCategoryIds = [];   // for homepage section
        $allProductIds  = [];
        $globalIdx = 1;

        foreach ($categoriesData as $idx => $catData) {
            $catId = DB::table('categories')->insertGetId([
                'name'       => $catData['name'],
                'slug'       => Str::slug($catData['name']),
                'parent_id'  => null,
                'image_url'  => $catData['image'],
                'is_active'  => true,
                'sort_order' => $idx + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $allCategoryIds[] = [
                'id'       => $catId,
                'name'     => $catData['name'],
                'image'    => $catData['image'],
                'discount' => $catData['discount'],
            ];

            foreach ($catData['products'] as $prodData) {
                $pId = DB::table('products')->insertGetId([
                    'title'             => $prodData['title'],
                    'slug'              => Str::slug($prodData['title']),
                    'sku'               => 'SKU-' . str_pad($globalIdx, 7, '0', STR_PAD_LEFT),
                    'description'       => $prodData['desc'],
                    'short_description' => 'Premium AURA product. Fast delivery across Pakistan.',
                    'brand_id'          => $brandIds[$idx % count($brandIds)],
                    'label_id'          => ($globalIdx % 3 === 0 ? $labelNew : ($globalIdx % 3 === 1 ? $labelHot : $labelSale)),
                    'is_active'         => true,
                    'is_featured'       => true,
                    'specifications'    => json_encode(['Fabric' => 'Pure Cotton', 'Origin' => 'Pakistan', 'Care' => 'Machine Wash Cold']),
                    'highlights'        => json_encode(['Premium Tailoring', 'Eco-Friendly Dye', 'Fast Delivery']),
                    'wash_care'         => 'Machine wash cold. Do not bleach. Tumble dry low.',
                    'origin_country'    => 'Pakistan',
                    'meta_title'        => $prodData['title'] . ' | AURA',
                    'meta_description'  => $prodData['desc'],
                    'sort_order'        => $globalIdx,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                $allProductIds[] = $pId;

                DB::table('product_category')->insert(['product_id' => $pId, 'category_id' => $catId]);
                DB::table('product_collection')->insert(['product_id' => $pId, 'collection_id' => $collectionId]);
                if ($globalIdx <= 10) {
                    DB::table('product_collection')->insert(['product_id' => $pId, 'collection_id' => $collectionSummer]);
                }

                // 2 media images per product
                DB::table('product_media')->insert([
                    ['product_id' => $pId, 'path' => $prodData['img'], 'type' => 'image', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                    ['product_id' => $pId, 'path' => $prodData['img'], 'type' => 'image', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ]);

                // 2 variants per product
                for ($v = 1; $v <= 2; $v++) {
                    $size  = $sizeIds[array_rand($sizeIds)];
                    $color = $colorIds[array_rand($colorIds)];
                    $vId = DB::table('product_variants')->insertGetId([
                        'product_id' => $pId,
                        'size_id'    => $size,
                        'color_id'   => $color,
                        'price'      => $prodData['price'],
                        'sale_price' => $prodData['sale'],
                        'sku'        => "SKUV-$pId-$v",
                        'weight'     => 0.5,
                        'length'     => 15.0, 'width' => 10.0, 'height' => 2.0,
                        'is_active'  => true,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    foreach ($whIds as $whId) {
                        DB::table('inventory_items')->insert([
                            'variant_id'          => $vId,
                            'warehouse_id'        => $whId,
                            'quantity'            => rand(50, 200),
                            'reserved'            => rand(0, 5),
                            'low_stock_threshold' => 10,
                            'created_at'          => now(), 'updated_at' => now(),
                        ]);
                    }
                }
                $globalIdx++;
            }
        }

        // ─── STEP 11: Product Reviews ─────────────────────────────────────────
        for ($i = 1; $i <= 15; $i++) {
            DB::table('product_reviews')->insert([
                'product_id'     => $allProductIds[array_rand($allProductIds)],
                'user_id'        => $customerIds[array_rand($customerIds)],
                'rating'         => rand(4, 5),
                'title'          => 'Amazing Quality!',
                'comment'        => 'Outstanding fabric quality. Color remains vibrant after multiple washes. Fully recommended to everyone.',
                'status'         => 'approved',
                'is_verified'    => true,
                'helpful_votes'  => rand(1, 10),
                'created_at'     => now(), 'updated_at' => now(),
            ]);
        }

        // ─── STEP 12: 2 Sample Orders ─────────────────────────────────────────
        for ($i = 1; $i <= 2; $i++) {
            $uId = $customerIds[$i - 1];
            $oId = DB::table('orders')->insertGetId([
                'order_number'           => 'AURA-' . strtoupper(Str::random(8)),
                'user_id'                => $uId,
                'shipping_name'          => "Customer $i",
                'shipping_phone'         => '03330000000',
                'shipping_address_line_1'=> "$i Main Blvd, Lahore",
                'shipping_city'          => 'Lahore',
                'shipping_state'         => 'Punjab',
                'shipping_postal_code'   => '54000',
                'shipping_country'       => 'Pakistan',
                'billing_name'           => "Customer $i",
                'billing_phone'          => '03330000000',
                'billing_address_line_1' => "$i Main Blvd, Lahore",
                'billing_city'           => 'Lahore',
                'billing_state'          => 'Punjab',
                'billing_postal_code'    => '54000',
                'billing_country'        => 'Pakistan',
                'subtotal'               => 4999,
                'discount_amount'        => 0.00,
                'tax_amount'             => 200,
                'shipping_cost'          => 150,
                'total'                  => 5349,
                'payment_method'         => 'cod',
                'payment_status'         => 'pending',
                'status'                 => 'placed',
                'created_at'             => now(), 'updated_at' => now(),
            ]);
            DB::table('order_timeline')->insert([
                'order_id'    => $oId, 'status' => 'placed',
                'title'       => 'Order Placed Successfully',
                'description' => 'Your order has been placed and is awaiting processing.',
                'created_at'  => now(), 'updated_at' => now(),
            ]);
        }

        // ─── STEP 13: FAQs & Testimonials ────────────────────────────────────
        $faqs = [
            ['question' => 'What is your return policy?',         'answer' => 'We offer a hassle-free 14-day return policy. Items must be unused and in original packaging.', 'sort_order' => 1],
            ['question' => 'How long does delivery take?',        'answer' => 'Standard delivery takes 3-5 business days across Pakistan. Express options available at checkout.', 'sort_order' => 2],
            ['question' => 'Do you offer Cash on Delivery?',      'answer' => 'Yes! COD is available across all major cities in Pakistan with no extra charges.', 'sort_order' => 3],
            ['question' => 'How can I track my order?',           'answer' => 'Once shipped, you will receive a tracking number via email/SMS to track your order in real-time.', 'sort_order' => 4],
            ['question' => 'Are your products authentic?',        'answer' => 'Absolutely! All AURA products are 100% authentic and sourced directly from verified manufacturers.', 'sort_order' => 5],
        ];
        foreach ($faqs as $faq) {
            DB::table('faqs')->insert(array_merge($faq, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }

        $testimonials = [
            ['customer_name' => 'Ayesha Khan',  'avatar_url' => 'https://i.pravatar.cc/100?img=1', 'rating' => 5, 'comment' => 'Absolutely love my new outfit! The fabric quality is top-notch and delivery was super fast.', 'customer_title' => 'Verified Customer'],
            ['customer_name' => 'Ali Hassan',   'avatar_url' => 'https://i.pravatar.cc/100?img=3', 'rating' => 5, 'comment' => 'Best online shopping experience in Pakistan. AURA never disappoints. Highly recommended!',    'customer_title' => 'Regular Shopper'],
            ['customer_name' => 'Sara Ahmed',   'avatar_url' => 'https://i.pravatar.cc/100?img=5', 'rating' => 5, 'comment' => 'The ethnic wear collection is stunning. My sherwani arrived perfectly packed and on time.',    'customer_title' => 'Premium Member'],
            ['customer_name' => 'Usman Malik',  'avatar_url' => 'https://i.pravatar.cc/100?img=7', 'rating' => 5, 'comment' => 'Great value for money. The GenZ collection is fire! Will definitely order again.',              'customer_title' => 'Style Enthusiast'],
            ['customer_name' => 'Fatima Zahra', 'avatar_url' => 'https://i.pravatar.cc/100?img=9', 'rating' => 5, 'comment' => 'Amazing sports collection! The dry-fit tee is super comfortable during workouts. Love it.',      'customer_title' => 'Fitness Enthusiast'],
        ];
        foreach ($testimonials as $t) {
            DB::table('testimonials')->insert(array_merge($t, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }

        // ─── STEP 14: Homepage Layout ─────────────────────────────────────────
        $layoutId = DB::table('homepage_layouts')->insertGetId([
            'name'               => 'AURA Main Layout',
            'header_style'       => 'classic',
            'hero_style'         => 'slider',
            'category_style'     => 'grid',
            'product_card_style' => 'premium',
            'footer_style'       => 'advanced',
            'is_active'          => true,
            'created_at'         => now(), 'updated_at' => now(),
        ]);

        // ─── STEP 15: Homepage Sections ──────────────────────────────────────
        // Section 1: Hero Slider (4 different design styles stored; active one selectable)
        $heroSlides = [
            // Design 1 - Yellow Gradient (Classic)
            [
                'design'      => 'classic_gradient',
                'active'      => true,
                'label'       => 'Design 1: Classic Yellow Gradient',
                'height'      => '420px',
                'width'       => '100%',
                'bg'          => 'from-[#fae04b] via-[#fbd83b] to-[#fae66d]',
                'logoText'    => 'AURA',
                'logoColor'   => 'text-zinc-950',
                'title'       => 'Men\'s Fashion',
                'subtitle'    => 'STARTING AT',
                'price'       => 'Rs 999',
                'btnLabel'    => 'SHOP NOW',
                'btnUrl'      => '/catalog/?category=men',
                'images'      => [
                    'https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?q=80&w=500&auto=format&fit=crop',
                ],
            ],
            // Design 2 - Pink/Rose (Women)
            [
                'design'      => 'rose_pink',
                'active'      => true,
                'label'       => 'Design 2: Rose Pink Women\'s',
                'height'      => '420px',
                'width'       => '100%',
                'bg'          => 'from-[#ffd6e7] via-[#ffafd1] to-[#ff85bb]',
                'logoText'    => 'FWD',
                'logoColor'   => 'text-[#c2185b]',
                'title'       => 'Women\'s Collection',
                'subtitle'    => 'NEW ARRIVALS',
                'price'       => 'Rs 1499',
                'btnLabel'    => 'EXPLORE NOW',
                'btnUrl'      => '/catalog/?category=women',
                'images'      => [
                    'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1496747611176-843222e1e57c?q=80&w=500&auto=format&fit=crop',
                ],
            ],
            // Design 3 - Dark/Night (GenZ)
            [
                'design'      => 'dark_night',
                'active'      => true,
                'label'       => 'Design 3: Dark Night GenZ',
                'height'      => '420px',
                'width'       => '100%',
                'bg'          => 'from-[#1a1a2e] via-[#16213e] to-[#0f3460]',
                'logoText'    => 'GEN',
                'logoColor'   => 'text-[#e94560]',
                'title'       => 'GenZ Streetwear',
                'subtitle'    => 'TRENDING NOW',
                'price'       => 'Rs 2499',
                'btnLabel'    => 'SHOP GENZ',
                'btnUrl'      => '/catalog/?category=genz',
                'images'      => [
                    'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?q=80&w=500&auto=format&fit=crop',
                ],
            ],
            // Design 4 - Mint/Green (Sports)
            [
                'design'      => 'mint_sports',
                'active'      => true,
                'label'       => 'Design 4: Mint Green Sports',
                'height'      => '420px',
                'width'       => '100%',
                'bg'          => 'from-[#d4fc79] via-[#96e6a1] to-[#43e97b]',
                'logoText'    => 'FIT',
                'logoColor'   => 'text-[#1b5e20]',
                'title'       => 'Sports & Active',
                'subtitle'    => 'PERFORMANCE GEAR',
                'price'       => 'Rs 1299',
                'btnLabel'    => 'GET ACTIVE',
                'btnUrl'      => '/catalog/?category=sports',
                'images'      => [
                    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?q=80&w=500&auto=format&fit=crop',
                ],
            ],
        ];

        // Build categories array for the categories section
        $categoriesSection = array_map(fn($c) => [
            'id'       => $c['id'],
            'title'    => $c['name'],
            'image'    => $c['image'],
            'discount' => $c['discount'],
            'url'      => '/catalog/?category=' . Str::slug($c['name']),
        ], $allCategoryIds);

        // ── Build product snapshots for product grid sections ──────────────────
        // Pick first 8 products for featured, next 8 for trending, etc.
        $buildProductCards = function(array $ids) use ($categoriesData): array {
            $cards = [];
            $imgMap = [];
            foreach ($categoriesData as $cat) {
                foreach ($cat['products'] as $p) {
                    $imgMap[$p['title']] = ['img' => $p['img'], 'price' => $p['price'], 'sale' => $p['sale']];
                }
            }
            foreach ($ids as $idx => $pId) {
                $row = DB::table('products')->where('id', $pId)->first();
                if (!$row) continue;
                $variant = DB::table('product_variants')->where('product_id', $pId)->first();
                $media   = DB::table('product_media')->where('product_id', $pId)->orderBy('sort_order')->first();
                $cards[] = [
                    'id'        => $pId,
                    'title'     => $row->title,
                    'slug'      => $row->slug,
                    'image'     => $media?->path ?? '',
                    'price'     => $variant?->price ?? 0,
                    'sale_price'=> $variant?->sale_price ?? null,
                    'url'       => '/product/' . $row->slug,
                ];
            }
            return $cards;
        };

        $featuredCards  = $buildProductCards(array_slice($allProductIds, 0,  8));
        $trendingCards  = $buildProductCards(array_slice($allProductIds, 2,  8));
        $bestSellerCards= $buildProductCards(array_slice($allProductIds, 4,  8));
        $newArrivalCards= $buildProductCards(array_slice($allProductIds, 12, 8));

        $sections = [
            // 1. Hero Slider - 4 designs with individual active/inactive toggle + height/width
            [
                'layout_id'        => $layoutId,
                'section_key'      => 'hero_slider',
                'title'            => 'Hero Slideshow',
                'subtitle'         => 'Main Banner',
                'description'      => 'Homepage hero slider with 4 beautiful designs.',
                'background_type'  => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding'          => 'py-6',
                'margin'           => 'my-0',
                'width'            => 'full',
                'animation'        => 'fade',
                'button_text'      => 'SHOP NOW',
                'button_url'       => '/catalog/',
                'layout_variation' => 'default',
                'show_on_mobile'   => true,
                'show_on_desktop'  => true,
                'sort_order'       => 1,
                'is_enabled'       => true,
                'settings'         => json_encode([
                    'slider_height'  => '420px',
                    'slider_width'   => '100%',
                    'autoplay'       => true,
                    'autoplay_delay' => 4500,
                    'slides'         => $heroSlides,
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],
            // 2. Bank Offers Slider
            [
                'layout_id'        => $layoutId,
                'section_key'      => 'bank_offers',
                'title'            => 'Exclusive Bank Offers',
                'subtitle'         => 'Save More With Our Partners',
                'description'      => 'Bank discount offers and cashback promos.',
                'background_type'  => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding'          => 'py-4',
                'margin'           => 'my-0',
                'width'            => 'container',
                'animation'        => 'slide',
                'button_text'      => null,
                'button_url'       => null,
                'layout_variation' => 'slider',
                'show_on_mobile'   => true,
                'show_on_desktop'  => true,
                'sort_order'       => 2,
                'is_enabled'       => true,
                'settings'         => json_encode([
                    'offers' => [
                        ['type' => 'hdfc',        'discount' => '10% INSTANT DISCOUNT', 'desc' => 'On HDFC Credit Cards. Min order Rs 4,999.'],
                        ['type' => 'bob-kotak',   'discount' => 'Rs 500 OFF',            'desc' => 'On BOBCARD & Kotak. Min cart Rs 2,999.'],
                        ['type' => 'flipkart-sbi','discount' => '5% CASHBACK',           'desc' => 'On SBI Credit & Debit Cards. All orders.'],
                        ['type' => 'scb',         'discount' => '15% OFF',               'desc' => 'Standard Chartered Cards. Max discount Rs 1,000.'],
                    ],
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],
            // 3. Categories Section
            [
                'layout_id'        => $layoutId,
                'section_key'      => 'categories',
                'title'            => 'Shop By Category',
                'subtitle'         => 'Explore All Styles',
                'description'      => 'Browse our complete range of fashion categories.',
                'background_type'  => 'color',
                'background_color' => '#fdf2f4',
                'background_image' => '',
                'background_video' => '',
                'padding'          => 'py-12',
                'margin'           => 'my-0',
                'width'            => 'container',
                'animation'        => 'fade',
                'button_text'      => 'VIEW ALL',
                'button_url'       => '/catalog/',
                'layout_variation' => 'grid',
                'show_on_mobile'   => true,
                'show_on_desktop'  => true,
                'sort_order'       => 3,
                'is_enabled'       => true,
                'settings'         => json_encode([
                    'categories' => $categoriesSection,
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],
            // 4. Budget Bargains
            [
                'layout_id'        => $layoutId,
                'section_key'      => 'budget_bargains',
                'title'            => 'Budget Bargains',
                'subtitle'         => 'Best Deals Under Rs 999',
                'description'      => 'Unbeatable prices on premium styles.',
                'background_type'  => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding'          => 'py-12',
                'margin'           => 'my-0',
                'width'            => 'container',
                'animation'        => 'fade',
                'button_text'      => 'SHOP ALL DEALS',
                'button_url'       => '/catalog/',
                'layout_variation' => 'grid',
                'show_on_mobile'   => true,
                'show_on_desktop'  => true,
                'sort_order'       => 4,
                'is_enabled'       => true,
                'settings'         => json_encode([
                    'items' => [
                        ['title' => 'Men Under Rs 999',    'tagline' => 'Top Rated Styles', 'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=400&auto=format&fit=crop'],
                        ['title' => 'Women Under Rs 999',  'tagline' => 'Everyday Favourites', 'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=400&auto=format&fit=crop'],
                        ['title' => 'Kids Under Rs 799',   'tagline' => 'Cute & Comfy', 'image' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?q=80&w=400&auto=format&fit=crop'],
                        ['title' => 'Accessories Rs 499',  'tagline' => 'Complete Your Look', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=400&auto=format&fit=crop'],
                    ],
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],
            // 5. Promo Banners
            [
                'layout_id'        => $layoutId,
                'section_key'      => 'promo_banners',
                'title'            => 'Festive Season Sale',
                'subtitle'         => 'Exclusive Deals Just For You',
                'description'      => 'Don\'t miss our biggest sale of the season.',
                'background_type'  => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding'          => 'py-10',
                'margin'           => 'my-0',
                'width'            => 'container',
                'animation'        => 'fade',
                'button_text'      => 'EXPLORE NOW',
                'button_url'       => '/catalog/',
                'layout_variation' => 'default',
                'show_on_mobile'   => true,
                'show_on_desktop'  => true,
                'sort_order'       => 5,
                'is_enabled'       => true,
                'settings'         => json_encode([
                    'banners' => [
                        ['title' => 'New Season Collection', 'subtitle' => 'Fresh styles just dropped', 'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=900&auto=format&fit=crop', 'buttonText' => 'SHOP WOMEN', 'buttonUrl' => '/catalog/?category=women'],
                        ['title' => "Men's Premium Edit", 'subtitle' => 'Curated for the modern man', 'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=900&auto=format&fit=crop', 'buttonText' => 'SHOP MEN', 'buttonUrl' => '/catalog/?category=men'],
                    ],
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 6. Featured Products
            [
                'layout_id' => $layoutId, 'section_key' => 'featured_products',
                'title' => 'Featured Products', 'subtitle' => 'Handpicked Just For You',
                'description' => 'Our editors top picks this season.',
                'background_type' => 'color', 'background_color' => '#f8fafc',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-12', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => 'VIEW ALL', 'button_url' => '/catalog/',
                'layout_variation' => 'grid', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 6, 'is_enabled' => true,
                'settings' => json_encode(['api_url' => '/api/v1/products?per_page=8&sort=recommended', 'limit' => 8]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 7. Trending Products
            [
                'layout_id' => $layoutId, 'section_key' => 'trending_products',
                'title' => 'Trending Right Now', 'subtitle' => 'What Everyone Is Buying',
                'description' => 'The hottest products flying off the shelves.',
                'background_type' => 'color', 'background_color' => '#ffffff',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-12', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => 'SEE ALL TRENDING', 'button_url' => '/catalog/?sort=newest',
                'layout_variation' => 'carousel', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 7, 'is_enabled' => true,
                'settings' => json_encode(['api_url' => '/api/v1/products?per_page=8&sort=newest', 'limit' => 8]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 8. Best Sellers
            [
                'layout_id' => $layoutId, 'section_key' => 'best_sellers',
                'title' => 'Best Sellers', 'subtitle' => 'Top Rated By Our Customers',
                'description' => 'Products with the highest ratings and repeat purchases.',
                'background_type' => 'color', 'background_color' => '#fff9f0',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-12', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => 'ALL BEST SELLERS', 'button_url' => '/catalog/?sort=rating',
                'layout_variation' => 'grid', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 8, 'is_enabled' => true,
                'settings' => json_encode(['api_url' => '/api/v1/products?per_page=8&sort=rating', 'limit' => 8]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 9. New Arrivals
            [
                'layout_id' => $layoutId, 'section_key' => 'new_arrivals',
                'title' => 'New Arrivals', 'subtitle' => 'Just Dropped This Week',
                'description' => 'Fresh styles added daily. Be the first to shop.',
                'background_type' => 'color', 'background_color' => '#f0fdf4',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-12', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => 'SHOP NEW ARRIVALS', 'button_url' => '/catalog/?sort=newest',
                'layout_variation' => 'grid', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 9, 'is_enabled' => true,
                'settings' => json_encode(['api_url' => '/api/v1/products?per_page=8&sort=newest', 'limit' => 8]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 10. Flash Sale
            [
                'layout_id' => $layoutId, 'section_key' => 'flash_sale',
                'title' => 'Flash Sale', 'subtitle' => 'Limited Time Offer — Hurry Up!',
                'description' => 'Massive discounts for a limited time only.',
                'background_type' => 'color', 'background_color' => '#fff1f2',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-12', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => 'GRAB THE DEAL', 'button_url' => '/catalog/',
                'layout_variation' => 'grid', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 10, 'is_enabled' => true,
                'settings' => json_encode([
                    'end_time'    => date('c', strtotime('+2 days')),
                    'api_url'     => '/api/v1/products?per_page=6&sort=recommended',
                    'limit'       => 6,
                    'badge_text'  => 'FLASH SALE',
                    'badge_color' => '#ff3f6c',
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 11. Brand Carousel
            [
                'layout_id' => $layoutId, 'section_key' => 'brand_carousel',
                'title' => 'Top Brands', 'subtitle' => 'Shop Your Favourite Labels',
                'description' => 'Explore collections from premium fashion brands.',
                'background_type' => 'color', 'background_color' => '#ffffff',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-10', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'slide', 'button_text' => null, 'button_url' => null,
                'layout_variation' => 'carousel', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 11, 'is_enabled' => true,
                'settings' => json_encode([
                    'brands' => [
                        ['name' => 'AURA Premium', 'logo' => 'https://placehold.co/160x60/1a1a1a/ffffff?text=AURA',     'url' => '/catalog/?brand=aura-premium'],
                        ['name' => 'Vibe Wear',    'logo' => 'https://placehold.co/160x60/6366f1/ffffff?text=VIBE',     'url' => '/catalog/?brand=vibe-wear'],
                        ['name' => 'Denim Co',     'logo' => 'https://placehold.co/160x60/0369a1/ffffff?text=DENIM+CO', 'url' => '/catalog/?brand=denim-co'],
                        ['name' => 'Kids Zone',    'logo' => 'https://placehold.co/160x60/f59e0b/1a1a1a?text=KIDS',     'url' => '/catalog/?brand=kids-zone'],
                        ['name' => 'Beauty Lab',   'logo' => 'https://placehold.co/160x60/ec4899/ffffff?text=BEAUTY',   'url' => '/catalog/?brand=beauty-lab'],
                        ['name' => 'GenZ Street',  'logo' => 'https://placehold.co/160x60/0f172a/ffffff?text=GENZ',     'url' => '/catalog/?brand=genz-street'],
                    ],
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 12. Lookbook
            [
                'layout_id' => $layoutId, 'section_key' => 'lookbook',
                'title' => 'Style Lookbook', 'subtitle' => 'Get Inspired By Our Latest Looks',
                'description' => 'Curated fashion looks for every occasion.',
                'background_type' => 'color', 'background_color' => '#0f172a',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-14', 'margin' => 'my-0', 'width' => 'full',
                'animation' => 'fade', 'button_text' => 'EXPLORE LOOKBOOK', 'button_url' => '/catalog/',
                'layout_variation' => 'grid', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 12, 'is_enabled' => true,
                'settings' => json_encode([
                    'looks' => [
                        ['title' => 'Summer Brunch Look', 'tag' => 'Women',  'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600&auto=format&fit=crop', 'url' => '/catalog/?category=women'],
                        ['title' => 'Street Smart',       'tag' => 'Men',    'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=600&auto=format&fit=crop', 'url' => '/catalog/?category=men'],
                        ['title' => 'GenZ Vibes',         'tag' => 'GenZ',   'image' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=600&auto=format&fit=crop', 'url' => '/catalog/?category=genz'],
                        ['title' => 'Wedding Season',     'tag' => 'Ethnic', 'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=600&auto=format&fit=crop', 'url' => '/catalog/?category=ethnic-wear'],
                    ],
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 13. Testimonials
            [
                'layout_id' => $layoutId, 'section_key' => 'testimonials',
                'title' => 'What Our Customers Say', 'subtitle' => 'Real Reviews From Real Shoppers',
                'description' => 'Thousands of happy customers across Pakistan.',
                'background_type' => 'color', 'background_color' => '#fdf2f4',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-14', 'margin' => 'my-0', 'width' => 'container',
                'animation' => 'fade', 'button_text' => null, 'button_url' => null,
                'layout_variation' => 'carousel', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 13, 'is_enabled' => true,
                'settings' => json_encode(['api_url' => '/api/v1/testimonials']),
                'created_at' => now(), 'updated_at' => now(),
            ],

            // 14. Newsletter
            [
                'layout_id' => $layoutId, 'section_key' => 'newsletter',
                'title' => 'Get Exclusive Offers', 'subtitle' => 'Join 50,000+ Happy Shoppers',
                'description' => 'Subscribe and get Rs 300 OFF your first order.',
                'background_type' => 'color', 'background_color' => '#1a1a1a',
                'background_image' => '', 'background_video' => '',
                'padding' => 'py-16', 'margin' => 'my-0', 'width' => 'full',
                'animation' => 'fade', 'button_text' => 'SUBSCRIBE', 'button_url' => null,
                'layout_variation' => 'default', 'show_on_mobile' => true, 'show_on_desktop' => true,
                'sort_order' => 14, 'is_enabled' => true,
                'settings' => json_encode([
                    'coupon_code'   => 'WELCOME300',
                    'discount_text' => 'Rs 300 OFF your first order',
                ]),
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('homepage_sections')->insert($sections);

        // ─── STEP 16: All Settings ────────────────────────────────────────────
        $settings = [
            ['key' => 'site_name',              'value' => 'AURA Fashion',           'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_tagline',            'value' => 'Premium Fashion Platform','created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency',               'value' => 'Rs',                     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone',               'value' => 'Asia/Karachi',           'created_at' => now(), 'updated_at' => now()],
            ['key' => 'language',               'value' => 'en',                     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'active_homepage_layout', 'value' => (string)$layoutId,        'created_at' => now(), 'updated_at' => now()],
            ['key' => 'primary_color',          'value' => '#ff3f6c',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'secondary_color',        'value' => '#1a1a1a',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'success_color',          'value' => '#10b981',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'warning_color',          'value' => '#f59e0b',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'error_color',            'value' => '#ef4444',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'typography_font',        'value' => 'Outfit',                'created_at' => now(), 'updated_at' => now()],
            ['key' => 'button_style',           'value' => 'rounded',               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'border_radius',          'value' => '4px',                   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'container_width',        'value' => '1280px',                'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auth_email_password_enabled', 'value' => '1',              'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auth_email_otp_enabled',      'value' => '1',              'created_at' => now(), 'updated_at' => now()],
            ['key' => 'oauth_google_enabled',         'value' => '1',             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_cod_enabled',          'value' => '1',             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_stripe_enabled',       'value' => '1',             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_phone',          'value' => '0300-1234567',          'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email',          'value' => 'support@aura.com',      'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_address',        'value' => 'Gulberg, Lahore, Pakistan', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_facebook',        'value' => 'https://facebook.com/aura', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_instagram',       'value' => 'https://instagram.com/aura', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_twitter',         'value' => 'https://twitter.com/aura', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_youtube',         'value' => 'https://youtube.com/aura', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_copyright',       'value' => '© 2026 AURA Fashion. All Rights Reserved.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_about_text',      'value' => 'AURA is a premium fashion eCommerce platform with the best brands at unbeatable prices.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_col1_title',      'value' => 'ONLINE SHOPPING',       'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_col1_links',      'value' => "Men\nWomen\nKids\nHome & Living\nBeauty\nGenZ\nFootwear\nSports\nAccessories\nEthnic Wear", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_col2_title',      'value' => 'CUSTOMER POLICIES',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_col2_links',      'value' => "Contact Us\nFAQ\nT&C\nTrack Orders\nShipping\nReturns\nPrivacy Policy\nCancellation", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_phone',           'value' => '0300-1234567',          'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_email',           'value' => 'support@aura.com',      'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_address',         'value' => 'Gulberg, Lahore, Pakistan', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_popular_searches','value' => 'Lawn Suits | Dresses For Girls | T-Shirts | Sneakers | Bags | Sport Shoes | Ethnic Wear | Kids Fashion', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'header_announcement_bar','value' => 'FLAT Rs. 500 OFF + FREE SHIPPING ON ORDERS ABOVE Rs. 2000 | USE CODE: AURA500', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_about_us',          'value' => "# About AURA Fashion\n\nWelcome to AURA - Pakistan's premium fashion destination. We bring you the finest apparel, accessories, and lifestyle products from top brands.\n\n## Our Story\nFounded in 2026, AURA was built to make premium fashion accessible to everyone across Pakistan with fast delivery and easy returns.\n\n## Contact\n- Email: support@aura.com\n- Phone: 0300-1234567\n- Address: Gulberg, Lahore, Pakistan", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_terms_conditions',  'value' => "# Terms & Conditions\n\nBy using AURA, you agree to our terms.\n\n## Orders\nWe reserve the right to cancel orders. Prices may change without notice.\n\n## Contact\nsupport@aura.com", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_privacy_policy',    'value' => "# Privacy Policy\n\nYour privacy matters to us. We collect minimal data to process orders and improve your experience.\n\n## Data We Collect\nName, email, address, and order information only.\n\n## Contact\nsupport@aura.com", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_shipping_policy',   'value' => "# Shipping Policy\n\n## Delivery Times\n- Standard (3-5 days): Free on orders above Rs. 2,000\n- Express (1-2 days): Rs. 300 flat\n\n## Coverage\nAll major cities across Pakistan.", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_return_policy',     'value' => "# Return Policy\n\n14-day hassle-free returns on all unused items in original packaging.\n\n## How to Return\nGo to Orders > Select Item > Initiate Return\n\nRefunds processed in 5-7 business days.", 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'page_cancellation_policy','value' => "# Cancellation Policy\n\nCancel anytime before the order is packed. Once shipped, cancellation is not possible.\n\nFull refund issued within 5 working days.", 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('settings')->insert($settings);

        $this->command->info('✅ DatabaseSeeder complete!');
        $this->command->info('📦 10 categories + 20 products seeded');
        $this->command->info('🏠 14 homepage sections seeded (all active)');
        $this->command->info('👤 Admin: admin@aura.com / password');
    }
}
