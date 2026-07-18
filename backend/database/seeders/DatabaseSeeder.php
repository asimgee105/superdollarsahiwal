<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with enterprise demo records.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean fresh truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Check if admin user already exists
        $adminUserExists = DB::table('users')->where('email', 'admin@superdollarsahiwal.com')->exists();
        $passwordHash = Hash::make('password');

        if (!$adminUserExists) {
            DB::table('permissions')->truncate();
            DB::table('roles')->truncate();
            DB::table('role_has_permissions')->truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_permissions')->truncate();
            DB::table('users')->truncate();
            DB::table('user_profiles')->truncate();
        } else {
            // Delete customer users but keep admins and managers
            $adminEmails = ['admin@superdollarsahiwal.com', 'admin1@example.com', 'admin2@example.com'];
            $customerUserIds = DB::table('users')
                ->whereNotIn('email', $adminEmails)
                ->where('email', 'not like', 'manager%@example.com')
                ->pluck('id')
                ->toArray();

            if (!empty($customerUserIds)) {
                DB::table('users')->whereIn('id', $customerUserIds)->delete();
                DB::table('user_profiles')->whereIn('user_id', $customerUserIds)->delete();
                DB::table('model_has_roles')->whereIn('model_id', $customerUserIds)->where('model_type', 'App\Models\User')->delete();
            }
        }

        DB::table('addresses')->truncate();
        DB::table('brands')->truncate();
        DB::table('categories')->truncate();
        DB::table('collections')->truncate();
        DB::table('homepage_layouts')->truncate();
        DB::table('homepage_sections')->truncate();
        DB::table('sizes')->truncate();
        DB::table('colors')->truncate();
        DB::table('product_labels')->truncate();
        DB::table('products')->truncate();
        DB::table('product_variants')->truncate();
        DB::table('product_media')->truncate();
        DB::table('product_category')->truncate();
        DB::table('product_collection')->truncate();
        DB::table('product_reviews')->truncate();
        DB::table('orders')->truncate();
        DB::table('order_items')->truncate();
        DB::table('order_transactions')->truncate();
        DB::table('order_timeline')->truncate();
        DB::table('coupons')->truncate();
        DB::table('posts')->truncate();
        DB::table('post_categories')->truncate();
        DB::table('post_category_pivot')->truncate();
        DB::table('faqs')->truncate();
        DB::table('testimonials')->truncate();
        DB::table('lookbooks')->truncate();
        DB::table('warehouses')->truncate();
        DB::table('inventory_items')->truncate();
        DB::table('return_requests')->truncate();
        DB::table('settings')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if (!$adminUserExists) {
            // Permissions & Roles
            $permissions = [
                ['name' => 'manage-users', 'guard_name' => 'web'],
                ['name' => 'view-dashboard', 'guard_name' => 'web'],
                ['name' => 'configure-settings', 'guard_name' => 'web'],
            ];
            DB::table('permissions')->insert($permissions);

            $roles = [
                ['name' => 'Super Admin', 'guard_name' => 'web'],
                ['name' => 'Administrator', 'guard_name' => 'web'],
                ['name' => 'Manager', 'guard_name' => 'web'],
                ['name' => 'Support', 'guard_name' => 'web'],
                ['name' => 'Customer', 'guard_name' => 'web'],
            ];
            DB::table('roles')->insert($roles);

            $superAdminRoleId = DB::table('roles')->where('name', 'Super Admin')->value('id');
            $adminRoleId = DB::table('roles')->where('name', 'Administrator')->value('id');
            $managerRoleId = DB::table('roles')->where('name', 'Manager')->value('id');

            $allPermissions = DB::table('permissions')->pluck('id')->toArray();
            foreach ($allPermissions as $permId) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permId,
                    'role_id' => $superAdminRoleId,
                ]);
            }

            // Super Admin
            $superAdminId = DB::table('users')->insertGetId([
                'name' => 'Super Admin User',
                'email' => 'admin@superdollarsahiwal.com',
                'password' => $passwordHash,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('model_has_roles')->insert([
                'role_id' => $superAdminRoleId,
                'model_type' => 'App\Models\User',
                'model_id' => $superAdminId,
            ]);
            DB::table('user_profiles')->insert([
                'user_id' => $superAdminId,
                'phone' => '03001234567',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 1 Admin
            $adminId = DB::table('users')->insertGetId([
                'name' => "Admin User 1",
                'email' => "admin1@example.com",
                'password' => $passwordHash,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('model_has_roles')->insert([
                'role_id' => $adminRoleId,
                'model_type' => 'App\Models\User',
                'model_id' => $adminId,
            ]);
            DB::table('user_profiles')->insert(['user_id' => $adminId, 'phone' => "03010000001", 'created_at' => now()]);

            // 1 Manager
            $managerId = DB::table('users')->insertGetId([
                'name' => "Manager 1",
                'email' => "manager1@example.com",
                'password' => $passwordHash,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('model_has_roles')->insert([
                'role_id' => $managerRoleId,
                'model_type' => 'App\Models\User',
                'model_id' => $managerId,
            ]);
            DB::table('user_profiles')->insert(['user_id' => $managerId, 'phone' => "03020000001", 'created_at' => now()]);
        } else {
            $superAdminId = DB::table('users')->where('email', 'admin@superdollarsahiwal.com')->value('id');
        }

        $customerRoleId = DB::table('roles')->where('name', 'Customer')->value('id');

        // Create 5 Customers
        $customerIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $id = DB::table('users')->insertGetId([
                'name' => "Customer Name $i",
                'email' => "customer$i@example.com",
                'password' => $passwordHash,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $customerIds[] = $id;

            DB::table('model_has_roles')->insert([
                'role_id' => $customerRoleId,
                'model_type' => 'App\Models\User',
                'model_id' => $id,
            ]);

            DB::table('user_profiles')->insert([
                'user_id' => $id,
                'phone' => '0333' . str_pad($id, 7, '0', STR_PAD_LEFT),
                'gender' => $i % 2 === 0 ? 'male' : 'female',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('addresses')->insert([
                [
                    'user_id' => $id,
                    'name' => 'Home address',
                    'type' => 'shipping',
                    'address_line_1' => "$id Main Boulevard street",
                    'city' => 'Lahore',
                    'state' => 'Punjab',
                    'postal_code' => '54000',
                    'country' => 'Pakistan',
                    'phone' => '0333' . str_pad($id, 7, '0', STR_PAD_LEFT),
                    'is_default' => true,
                ]
            ]);
        }

        // Create 5 Brands
        $brandIds = [];
        foreach (['AURA Premium', 'Vibe Wear', 'Denim Co', 'Kids Collection', 'Beauty Essentials'] as $brandName) {
            $brandIds[] = DB::table('brands')->insertGetId([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'description' => "This is a premium designer apparel brand: $brandName.",
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Main Categories
        $categoriesData = [
            'Men' => ['Apparel', 'Footwear'],
            'Women' => ['Apparel', 'Footwear'],
            'Kids' => ['Boys Clothing', 'Girls Clothing'],
            'Home' => ['Bed Linen', 'Decor'],
            'Beauty' => ['Makeup', 'Skincare'],
            'GenZ' => ['Streetwear', 'Oversized'],
        ];

        $categoryMap = []; // Maps leaf name to category ID
        foreach ($categoriesData as $parentName => $children) {
            $parentId = DB::table('categories')->insertGetId([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            foreach ($children as $childName) {
                $childId = DB::table('categories')->insertGetId([
                    'name' => $childName,
                    'slug' => Str::slug($parentName . '-' . $childName),
                    'parent_id' => $parentId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $categoryMap[$parentName][] = $childId;
            }
        }

        // Create Collections
        $collectionId = DB::table('collections')->insertGetId([
            'name' => 'New Arrivals',
            'slug' => 'new-arrivals',
            'description' => 'Dynamic selection matching new styles.',
            'is_active' => true,
            'created_at' => now(),
        ]);

        // Seed Sizes and Colors
        $sizeIds = [];
        foreach (['S', 'M', 'L', 'XL'] as $sizeName) {
            $sizeIds[] = DB::table('sizes')->insertGetId([
                'name' => $sizeName,
                'slug' => Str::slug($sizeName),
                'type' => 'clothing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $colorIds = [];
        foreach ([
            ['name' => 'Royal Black', 'hex_code' => '#000000'],
            ['name' => 'Cream White', 'hex_code' => '#ffffff'],
            ['name' => 'Classic Navy', 'hex_code' => '#000080'],
        ] as $color) {
            $colorIds[] = DB::table('colors')->insertGetId(array_merge($color, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Product Catalog definition - exactly 2 premium products per main category
        $productsToSeed = [
            'Men' => [
                [
                    'title' => 'AURA Premium Denim Jacket',
                    'price' => 4999,
                    'desc' => 'High quality premium denim jacket, hand-crafted in Pakistan. Offers stylish streetwear aesthetic.',
                    'img' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Smart Fit Oxford Shirt',
                    'price' => 2999,
                    'desc' => 'Classic smart fit oxford shirt. Breathable cotton blend ideal for formal or semi-formal wear.',
                    'img' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=600&auto=format&fit=crop'
                ]
            ],
            'Women' => [
                [
                    'title' => 'AURA Linen Kurta Set',
                    'price' => 5999,
                    'desc' => 'Premium 2-piece linen kurta set. Exquisite hand-woven embroidery details for everyday comfort.',
                    'img' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Summer Maxi Dress',
                    'price' => 3499,
                    'desc' => 'Charming and flowy cotton summer maxi dress with floral patterns. Light-weight and breathable.',
                    'img' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?q=80&w=600&auto=format&fit=crop'
                ]
            ],
            'Kids' => [
                [
                    'title' => 'AURA Boys Casual Polo Shirt',
                    'price' => 1499,
                    'desc' => 'Super soft cotton polo shirt for boys. Standard fit with clean collar and button setup.',
                    'img' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Girls Floral Cotton Dress',
                    'price' => 1999,
                    'desc' => 'Aesthetic girls floral dress. Soft organic cotton material designed for gentle skin.',
                    'img' => 'https://images.unsplash.com/photo-1607990283143-e81e7a2c93ab?q=80&w=600&auto=format&fit=crop'
                ]
            ],
            'Home' => [
                [
                    'title' => 'AURA Organic Cotton Bedsheet Set',
                    'price' => 7999,
                    'desc' => 'Luxurious 400 thread count organic cotton king size bedsheet set with matching pillow covers.',
                    'img' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Handwoven Ceramic Cushion Cover',
                    'price' => 1299,
                    'desc' => 'Artisanal handwoven texture cushion cover. Elevates your living room style instantly.',
                    'img' => 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?q=80&w=600&auto=format&fit=crop'
                ]
            ],
            'Beauty' => [
                [
                    'title' => 'AURA Organic Lip & Cheek Tint',
                    'price' => 999,
                    'desc' => 'Clean, organic formula lip and cheek tint. Natural rosy flush finish with long-lasting hydration.',
                    'img' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Citrus Infused Body Mist',
                    'price' => 1499,
                    'desc' => 'Refreshing citrus infused daily body mist. Keeps you fresh and clean all day.',
                    'img' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=600&auto=format&fit=crop'
                ]
            ],
            'GenZ' => [
                [
                    'title' => 'AURA Oversized Graphic Hoodie',
                    'price' => 3999,
                    'desc' => 'Retro aesthetic oversized graphic hoodie. Comfy brushed fleece inside, drop shoulders.',
                    'img' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=600&auto=format&fit=crop'
                ],
                [
                    'title' => 'AURA Baggy Retro Utility Jeans',
                    'price' => 4500,
                    'desc' => 'High quality retro baggy utility jeans. Multi-pocket design, premium dark indigo wash.',
                    'img' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?q=80&w=600&auto=format&fit=crop'
                ]
            ],
        ];

        $productIds = [];
        $globalIdx = 1;

        foreach ($productsToSeed as $parentCatName => $items) {
            $catIds = $categoryMap[$parentCatName];
            
            foreach ($items as $itemData) {
                $pId = DB::table('products')->insertGetId([
                    'title' => $itemData['title'],
                    'slug' => Str::slug($itemData['title']),
                    'sku' => 'SKU-' . str_pad($globalIdx, 7, '0', STR_PAD_LEFT),
                    'description' => $itemData['desc'],
                    'short_description' => "Premium AURA Style product.",
                    'brand_id' => $brandIds[0], // First brand as default
                    'is_active' => true,
                    'is_featured' => true,
                    'specifications' => json_encode(['Fabric' => 'Pure Cotton', 'Origin' => 'Pakistan']),
                    'highlights' => json_encode(['Premium Tailoring', 'Eco-Friendly Dye']),
                    'wash_care' => 'Dry clean recommended.',
                    'origin_country' => 'Pakistan',
                    'meta_title' => $itemData['title'],
                    'meta_description' => 'Premium high-quality apparel option.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $productIds[] = $pId;

                // Associate product with child categories
                foreach ($catIds as $cId) {
                    DB::table('product_category')->insert([
                        'product_id' => $pId,
                        'category_id' => $cId,
                    ]);
                }
                
                // Link to collections
                DB::table('product_collection')->insert([
                    'product_id' => $pId,
                    'collection_id' => $collectionId,
                ]);

                // Media urls (2 images per product)
                DB::table('product_media')->insert([
                    [
                        'product_id' => $pId,
                        'path' => $itemData['img'],
                        'type' => 'image',
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'product_id' => $pId,
                        'path' => $itemData['img'], // secondary
                        'type' => 'image',
                        'sort_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);

                $globalIdx++;
            }
        }

        // Create Warehouses and stock allocations
        $warehouses = [
            ['name' => 'Karachi Fulfilment Hub', 'code' => 'KHI-01', 'address' => 'S.I.T.E Karachi', 'is_active' => true],
            ['name' => 'Lahore Central Warehouse', 'code' => 'LHR-02', 'address' => 'Gulberg Lahore', 'is_active' => true],
        ];
        $whIds = [];
        foreach ($warehouses as $wh) {
            $whIds[] = DB::table('warehouses')->insertGetId(array_merge($wh, ['created_at' => now()]));
        }

        // Generate Variants and inventory for each product
        foreach ($productIds as $pId) {
            for ($v = 1; $v <= 2; $v++) {
                $size = $sizeIds[array_rand($sizeIds)];
                $color = $colorIds[array_rand($colorIds)];
                $price = rand(1500, 8000);
                
                $vId = DB::table('product_variants')->insertGetId([
                    'product_id' => $pId,
                    'size_id' => $size,
                    'color_id' => $color,
                    'price' => $price,
                    'sale_price' => $price - 300,
                    'sku' => "SKUV-$pId-$size-$color-$v",
                    'weight' => 0.5,
                    'length' => 15.00,
                    'width' => 10.00,
                    'height' => 2.00,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($whIds as $whId) {
                    DB::table('inventory_items')->insert([
                        'variant_id' => $vId,
                        'warehouse_id' => $whId,
                        'quantity' => rand(50, 100),
                        'reserved' => rand(0, 5),
                        'low_stock_threshold' => 10,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Seed 10 Product Reviews
        for ($i = 1; $i <= 10; $i++) {
            DB::table('product_reviews')->insert([
                'product_id' => $productIds[array_rand($productIds)],
                'user_id' => $customerIds[array_rand($customerIds)],
                'rating' => 5,
                'title' => 'Amazing Quality',
                'comment' => 'Outstanding fabric quality, color remains vibrant after multiple washes. Fully recommended.',
                'status' => 'approved',
                'is_verified' => true,
                'helpful_votes' => rand(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 2 Orders
        for ($i = 1; $i <= 2; $i++) {
            $uId = $customerIds[array_rand($customerIds)];
            $oId = DB::table('orders')->insertGetId([
                'order_number' => 'AURA-TRK-7483921' . $i,
                'user_id' => $uId,
                'shipping_name' => "Customer Name $i",
                'shipping_phone' => '03330000000',
                'shipping_address_line_1' => 'Gulberg, Lahore, Pakistan',
                'shipping_city' => 'Lahore',
                'shipping_state' => 'Punjab',
                'shipping_postal_code' => '54000',
                'shipping_country' => 'Pakistan',
                'billing_name' => "Customer Name $i",
                'billing_phone' => '03330000000',
                'billing_address_line_1' => 'Gulberg, Lahore, Pakistan',
                'billing_city' => 'Lahore',
                'billing_state' => 'Punjab',
                'billing_postal_code' => '54000',
                'billing_country' => 'Pakistan',
                'subtotal' => 2499,
                'discount_amount' => 0.00,
                'tax_amount' => 100,
                'shipping_cost' => 150,
                'total' => 2749,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status' => 'placed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_timeline')->insert([
                'order_id' => $oId,
                'status' => 'placed',
                'title' => 'Order Placed Successfully',
                'description' => 'The order has been created and is awaiting processing confirmation.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed homepage layouts and layout manager configurations
        $activeLayoutId = DB::table('homepage_layouts')->insertGetId([
            'name' => 'AURA Enterprise Premium Layout',
            'header_style' => 'classic',
            'hero_style' => 'slider',
            'category_style' => 'grid',
            'product_card_style' => 'premium',
            'footer_style' => 'advanced',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed basic homepage sections
        DB::table('homepage_sections')->insert([
            [
                'layout_id' => $activeLayoutId,
                'section_key' => 'promo_banners',
                'title' => 'Festive Season Arrivals',
                'subtitle' => 'Exclusive Autumn Collection 2026',
                'description' => 'Browse through premium apparel from world-class designers.',
                'background_type' => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding' => 'py-12',
                'margin' => 'my-0',
                'width' => 'container',
                'animation' => 'fade',
                'button_text' => 'SHOP NOW',
                'button_url' => '/catalog/',
                'layout_variation' => 'default',
                'show_on_mobile' => true,
                'show_on_desktop' => true,
                'sort_order' => 1,
                'is_enabled' => true,
                'settings' => json_encode([
                    'banners' => [
                        [
                            'title' => 'Denim Essentials',
                            'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600&auto=format&fit=crop',
                            'buttonText' => 'EXPLORE',
                            'buttonUrl' => '/catalog/'
                        ]
                    ]
                ]),
            ],
            [
                'layout_id' => $activeLayoutId,
                'section_key' => 'bank_offers',
                'title' => 'Exclusive Bank Promos',
                'subtitle' => 'Cashback & Discounts',
                'description' => 'Save more with our banking partners.',
                'background_type' => 'color',
                'background_color' => '#ffffff',
                'background_image' => '',
                'background_video' => '',
                'padding' => 'py-6',
                'margin' => 'my-0',
                'width' => 'container',
                'animation' => 'slide',
                'button_text' => null,
                'button_url' => null,
                'layout_variation' => 'slider',
                'show_on_mobile' => true,
                'show_on_desktop' => true,
                'sort_order' => 2,
                'is_enabled' => true,
                'settings' => json_encode([
                    'offers' => [
                        [
                            'type' => 'hdfc',
                            'discount' => '10% INSTANT DISCOUNT',
                            'desc' => 'On HDFC Credit Cards. Valid on all orders above Rs. 4,999.'
                        ]
                    ]
                ]),
            ]
        ]);

        // Seed basic settings
        $settings = [
            ['key' => 'site_name', 'value' => 'AURA Enterprise', 'created_at' => now()],
            ['key' => 'site_tagline', 'value' => 'Ultimate Fashion Platform', 'created_at' => now()],
            ['key' => 'currency', 'value' => 'PKR', 'created_at' => now()],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'created_at' => now()],
            ['key' => 'language', 'value' => 'en', 'created_at' => now()],
            ['key' => 'active_homepage_layout', 'value' => (string)$activeLayoutId, 'created_at' => now()],
            
            // Theme default colors & fonts
            ['key' => 'primary_color', 'value' => '#ff3f6c', 'created_at' => now()],
            ['key' => 'secondary_color', 'value' => '#1a1a1a', 'created_at' => now()],
            ['key' => 'success_color', 'value' => '#10b981', 'created_at' => now()],
            ['key' => 'warning_color', 'value' => '#f59e0b', 'created_at' => now()],
            ['key' => 'error_color', 'value' => '#ef4444', 'created_at' => now()],
            ['key' => 'typography_font', 'value' => 'Outfit', 'created_at' => now()],
            ['key' => 'button_style', 'value' => 'rounded', 'created_at' => now()],
            ['key' => 'border_radius', 'value' => '4px', 'created_at' => now()],
            ['key' => 'container_width', 'value' => '1280px', 'created_at' => now()],
            
            // Social Auth
            ['key' => 'oauth_google_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'oauth_apple_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'phone_login_enabled', 'value' => '0', 'created_at' => now()],
            
            // Payment Gateways
            ['key' => 'payment_cod_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'payment_stripe_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'payment_googlepay_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'payment_paypal_enabled', 'value' => '1', 'created_at' => now()],
            ['key' => 'payment_applepay_enabled', 'value' => '1', 'created_at' => now()],
            
            // Custom Footer Settings
            ['key' => 'footer_copyright', 'value' => '© 2026 AURA Commerce. All Rights Reserved.', 'created_at' => now()],
            ['key' => 'footer_about_text', 'value' => 'AURA is a premium high-fidelity enterprise eCommerce suite.', 'created_at' => now()],
            ['key' => 'footer_col1_title', 'value' => 'ONLINE SHOPPING', 'created_at' => now()],
            ['key' => 'footer_col1_links', 'value' => "Men\nWomen\nKids\nHome & Living\nBeauty\nGenz", 'created_at' => now()],
            ['key' => 'footer_col2_title', 'value' => 'CUSTOMER POLICIES', 'created_at' => now()],
            ['key' => 'footer_col2_links', 'value' => "Contact Us\nFAQ\nT&C\nTrack Orders\nShipping\nPrivacy Policy", 'created_at' => now()],
            ['key' => 'footer_address', 'value' => 'Gulberg, Lahore, Pakistan', 'created_at' => now()],
            ['key' => 'footer_phone', 'value' => '0300-1234567', 'created_at' => now()],
            ['key' => 'footer_email', 'value' => 'support@aura.com', 'created_at' => now()],
            ['key' => 'footer_popular_searches', 'value' => 'Makeup | Dresses For Girls | T-Shirts | Sandals | Bags | Sport Shoes', 'created_at' => now()],

            // Default Page Contents (Markdown/HTML)
            ['key' => 'page_about_us', 'value' => "# About AURA Enterprise\n\nWelcome to AURA, the ultimate destination for fashion, style, and luxury. AURA is a high-fidelity enterprise eCommerce suite built to provide an unparalleled shopping experience.\n\n## Our Story\nFounded in 2026, AURA was created to bridge the gap between high-end fashion design and advanced digital commerce. We select only the finest fabrics and collaborate with world-class tailors to deliver premium garments to your doorstep.\n\n## Contact Information\nIf you have any questions or feedback, please contact us at **support@aura.com** or call us at **0300-1234567**.", 'created_at' => now()],
            ['key' => 'page_terms_conditions', 'value' => "# Terms & Conditions\n\nWelcome to AURA. By accessing or using our website, you agree to comply with and be bound by the following terms and conditions of use.\n\n## 1. General\nThe content of this website is for your general information and use only. It is subject to change without notice.\n\n## 2. Order Acceptance\nWe reserve the right to refuse or cancel any order for any reason, including limitations on quantities available for purchase, inaccuracies in product or pricing information, or problems identified by our credit and fraud avoidance department.\n\n## 3. Contact Us\nIf you have any questions about these Terms, please contact us at **support@aura.com**.", 'created_at' => now()],
            ['key' => 'page_privacy_policy', 'value' => "# Privacy Policy\n\nAt AURA, we are committed to protecting your privacy. This Privacy Policy details how we collect, use, and safeguard your personal information.\n\n## 1. Information Collection\nWe collect personal information when you register, place an order, subscribe to our newsletter, or browse our website. This includes your name, email, phone number, shipping address, and payment information.\n\n## 2. Information Usage\nWe use your information to process transactions, improve our customer service, send periodic order update emails, and personalize your shopping experience.\n\n## 3. Data Protection\nWe implement a variety of security measures to maintain the safety of your personal information. Your data is encrypted and handled securely.", 'created_at' => now()],
            ['key' => 'page_shipping_policy', 'value' => "# Shipping Policy\n\nThank you for shopping at AURA. Here are the terms and conditions that constitute our Shipping Policy.\n\n## 1. Shipment Processing Time\nAll orders are processed within 1-2 business days. Orders are not shipped or delivered on weekends or public holidays.\n\n## 2. Shipping Rates & Delivery Estimates\nShipping charges for your orders will be calculated and displayed at checkout:\n- **Standard Shipping (TCS / Leopards)**: Free on orders above Rs. 3,000. Flat rate Rs. 150 otherwise.\n- **Delivery Time**: 3-5 business days across Pakistan.\n\n## 3. Shipping Confirmation & Order Tracking\nYou will receive a Shipment Confirmation email once your order has shipped containing your tracking number.", 'created_at' => now()],
            ['key' => 'page_return_policy', 'value' => "# Return & Exchange Policy\n\nAt AURA, we want you to be completely satisfied with your purchase. We offer a hassle-free **14-day return and exchange policy**.\n\n## 1. Conditions for Returns\nTo be eligible for a return or exchange, your item must be:\n- Unused, unwashed, and in the same condition that you received it.\n- In its original packaging with all product labels and price tags intact.\n\n## 2. How to Initiate a Return\nSimply log in to your account, visit the Orders section, and click on **Initiate Return** or email us at **support@aura.com** with your order number.\n\n## 3. Refunds\nOnce we receive and inspect your returned item, we will process your refund to your original payment method or as store credit within 5-7 business days.", 'created_at' => now()],
            ['key' => 'page_refund_policy', 'value' => "# Refund Policy\n\nOur Refund Policy describes the conditions under which refunds are issued for purchases made at AURA.\n\n## 1. Refund Eligibility\nRefunds are processed for returned items that pass our quality inspection. In case of damaged or incorrect items delivered, a full refund including shipping costs will be issued.\n\n## 2. Processing Timeline\nRefunds are processed within 5-7 working days of receiving the returned package. The funds will be credited back to your bank account, credit card, or digital wallet.\n\n## 3. Contact Us\nFor any refund-related queries, please write to us at **support@aura.com**.", 'created_at' => now()],
            ['key' => 'page_cookie_policy', 'value' => "# Cookie Policy\n\nThis Cookie Policy explains how AURA uses cookies and similar tracking technologies to personalize and enhance your browsing experience.\n\n## 1. What are Cookies?\nCookies are small text files stored on your browser or device when you visit a website. They help us remember your preferences, keep you logged in, and analyze website traffic.\n\n## 2. How We Use Cookies\nWe use essential cookies to manage shopping carts, secure login sessions, and track checkout state. We also use analytics cookies to monitor platform performance.\n\n## 3. Managing Cookies\nYou can choose to disable cookies through your browser settings; however, please note that some parts of the store may not function correctly.", 'created_at' => now()],
            ['key' => 'page_cancellation_policy', 'value' => "# Order Cancellation Policy\n\nAt AURA, we understand that plans change. You can cancel your order under the following terms:\n\n## 1. Cancellation Window\nYou can cancel your order free of charge at any time **before the order has been packed or shipped**. Once the order status is updated to 'Packed' or 'Shipped', cancellation is no longer possible.\n\n## 2. How to Cancel\nTo cancel an order, navigate to your Profile -> Orders panel, select the order, and click the **Cancel Order** button, or call our customer service hotline.\n\n## 3. Refunds on Cancellations\nIf you cancel a prepaid order, the full transaction amount will be refunded to your account within 5 working days.", 'created_at' => now()],
            ['key' => 'page_careers', 'value' => "# Careers at AURA\n\nJoin the team building the future of premium high-fidelity digital fashion commerce.\n\n## Why Work at AURA?\nWe offer a creative, collaborative, and fast-paced environment where your work has a direct impact. Enjoy flexible working hours, medical benefits, and employee discounts.\n\n## Open Positions\n1. **Senior Frontend React Engineer** (Lahore / Remote)\n2. **UI/UX Product Designer** (Karachi / Hybrid)\n3. **Content Creator & Brand Strategist** (Lahore / Hybrid)\n\n## How to Apply\nSend your portfolio and resume to **careers@aura.com** with the job title as the subject line.", 'created_at' => now()],
            ['key' => 'page_contact_info', 'value' => "# Contact Us\n\nWe would love to hear from you. Get in touch with our customer success team for any questions or support.\n\n## Get In Touch\n- **Email**: support@aura.com\n- **Phone**: 0300-1234567\n- **Address**: Gulberg, Lahore, Pakistan\n\n## Operating Hours\nMonday to Saturday: 9:00 AM - 6:00 PM (PKT)", 'created_at' => now()],
        ];

        DB::table('settings')->insert($settings);
    }
}
