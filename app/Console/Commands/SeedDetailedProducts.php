<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Str;

class SeedDetailedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed detailed products, categories, brands, and knowledge base articles for AI Customer Support testing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Seeding Categories...');

        $catElectronics = Category::firstOrCreate(['slug' => 'electronics-gadgets'], [
            'name'        => 'Electronics & Gadgets',
            'status'      => 1,
            'front_view'  => 1,
            'image'       => 'uploads/categories/electronics.png',
        ]);

        $catFashion = Category::firstOrCreate(['slug' => 'fashion-apparel'], [
            'name'        => 'Fashion & Apparel',
            'status'      => 1,
            'front_view'  => 1,
            'image'       => 'uploads/categories/fashion.png',
        ]);

        $catHome = Category::firstOrCreate(['slug' => 'home-kitchen-appliances'], [
            'name'        => 'Home & Kitchen Appliances',
            'status'      => 1,
            'front_view'  => 1,
            'image'       => 'uploads/categories/home.png',
        ]);

        $this->info('🌱 Seeding Brands...');

        $brandApple   = Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'name_bn' => 'অ্যাপল', 'status' => 1]);
        $brandSamsung = Brand::firstOrCreate(['slug' => 'samsung'], ['name' => 'Samsung', 'name_bn' => 'স্যামসাং', 'status' => 1]);
        $brandSony    = Brand::firstOrCreate(['slug' => 'sony'], ['name' => 'Sony', 'name_bn' => 'সনি', 'status' => 1]);
        $brandNike    = Brand::firstOrCreate(['slug' => 'nike'], ['name' => 'Nike', 'name_bn' => 'নাইকি', 'status' => 1]);
        $brandPhilips = Brand::firstOrCreate(['slug' => 'philips'], ['name' => 'Philips', 'name_bn' => 'ফিলিপস', 'status' => 1]);

        $this->info('🌱 Seeding Detailed Products...');

        $productsData = [
            [
                'name'             => 'iPhone 15 Pro Max 256GB',
                'slug'             => 'iphone-15-pro-max-256gb',
                'category_id'      => $catElectronics->id,
                'brand_id'         => $brandApple->id,
                'product_code'     => 'IPH-15PM-256',
                'purchase_price'   => 120000,
                'old_price'        => 155000,
                'new_price'        => 145000,
                'stock'            => 25,
                'sold'             => 14,
                'meta_description' => 'Buy official Apple iPhone 15 Pro Max 256GB Titanium with A17 Pro Chip, 48MP camera, and 5x optical zoom.',
                'description'      => 'The flagship iPhone 15 Pro Max featuring Titanium design, A17 Pro chip, 48MP main camera with 5x optical zoom, Action button, USB-C 3 speed support, and all-day battery life (up to 29 hours video playback). Available in Natural Titanium, Blue Titanium, and Black Titanium colors.',
                'features'         => '• 6.7-inch Super Retina XDR OLED Display (120Hz ProMotion)\n• A17 Pro 3nm 6-core GPU Processor\n• 8GB RAM | 256GB NVMe Storage\n• 48MP Main + 12MP Ultra Wide + 12MP 5x Telephoto Camera\n• Ceramic Shield Front, Titanium Frame, IP68 Water Resistance (6m up to 30 mins)\n• USB-C Port supporting USB 3 speeds (10Gbps)\n• 4422 mAh Battery with MagSafe 15W Fast Wireless Charging',
                'topsale'          => 1,
                'feature_product'  => 1,
                'flashsale'        => 1,
                'is_new'           => 1,
                'is_prime'         => 1,
                'status'           => 1,
                'image'            => 'uploads/products/iphone15promax.png',
            ],
            [
                'name'             => 'Samsung Galaxy S24 Ultra 5G (12GB RAM, 512GB)',
                'slug'             => 'samsung-galaxy-s24-ultra-5g-512gb',
                'category_id'      => $catElectronics->id,
                'brand_id'         => $brandSamsung->id,
                'product_code'     => 'SAM-S24U-512',
                'purchase_price'   => 125000,
                'old_price'        => 160000,
                'new_price'        => 148000,
                'stock'            => 18,
                'sold'             => 9,
                'meta_description' => 'Buy Samsung Galaxy S24 Ultra 5G 512GB with Galaxy AI, Built-in S-Pen, and 200MP Camera System.',
                'description'      => 'Samsung Galaxy S24 Ultra featuring Galaxy AI capabilities including Circle to Search, Live Translate, and Note Assist. Powered by Snapdragon 8 Gen 3 for Galaxy with 200MP Quad Telephoto camera system and integrated S Pen.',
                'features'         => '• 6.8-inch QHD+ Dynamic AMOLED 2X Display (1-120Hz, 2600 nits peak brightness)\n• Snapdragon 8 Gen 3 for Galaxy Processor\n• 12GB LPDDR5X RAM | 512GB UFS 4.0 Storage\n• Built-in S-Pen Stylus with Bluetooth Remote Control\n• 200MP Main + 50MP 5x Zoom + 10MP 3x Zoom + 12MP Ultra Wide Camera\n• Titanium Frame with Gorilla Glass Armor Display Protection\n• 5000 mAh Battery with 45W Fast Wired Charging',
                'topsale'          => 1,
                'feature_product'  => 1,
                'flashsale'        => 0,
                'is_new'           => 1,
                'is_prime'         => 1,
                'status'           => 1,
                'image'            => 'uploads/products/s24ultra.png',
            ],
            [
                'name'             => 'Sony WH-1000XM5 Wireless Noise-Canceling Headphones',
                'slug'             => 'sony-wh-1000xm5-wireless-headphones',
                'category_id'      => $catElectronics->id,
                'brand_id'         => $brandSony->id,
                'product_code'     => 'SONY-WH1000XM5',
                'purchase_price'   => 28000,
                'old_price'        => 42000,
                'new_price'        => 37500,
                'stock'            => 30,
                'sold'             => 22,
                'meta_description' => 'Sony WH-1000XM5 Premium Wireless Noise Canceling Headphones with 30-hour battery and Speak-to-Chat.',
                'description'      => 'Industry-leading noise canceling over-ear headphones with 8 microphones and Auto NC Optimizer. Features 30mm specially designed driver units, High-Resolution Wireless Audio via LDAC codec, and crystal-clear hands-free calling with precise voice pickup technology.',
                'features'         => '• Dual Processors (V1 Integrated + QN1 HD Noise Canceling Processor)\n• 8 Microphones for Auto NC Optimization\n• 30 Hours Battery Life (3 min charge = 3 hours listening time)\n• Speak-to-Chat & Quick Attention Mode\n• Multipoint Connection (Pair with 2 Bluetooth devices at once)\n• Ultra-comfortable Lightweight Synthetic Soft Fit Leather Cushioning',
                'topsale'          => 1,
                'feature_product'  => 1,
                'flashsale'        => 1,
                'is_new'           => 0,
                'is_prime'         => 0,
                'status'           => 1,
                'image'            => 'uploads/products/sony_wh1000xm5.png',
            ],
            [
                'name'             => 'Nike Air Zoom Pegasus 40 Running Shoes',
                'slug'             => 'nike-air-zoom-pegasus-40-running-shoes',
                'category_id'      => $catFashion->id,
                'brand_id'         => $brandNike->id,
                'product_code'     => 'NIKE-PEG-40',
                'purchase_price'   => 8500,
                'old_price'        => 14500,
                'new_price'        => 12900,
                'stock'            => 40,
                'sold'             => 31,
                'meta_description' => 'Buy original Nike Air Zoom Pegasus 40 breathable road running shoes with dual Zoom Air units.',
                'description'      => 'A springy ride for every run, the Pegasus 40 delivers a neutral support and balanced cushioning. Engineered single-layer mesh upper provides optimized ventilation and comfortable fit across foot shapes.',
                'features'         => '• Nike React Foam Technology + 2 Zoom Air Units (Forefoot & Heel)\n• Redesigned Midfoot Strap for a Molded Fit\n• Single-layer Breathable Engineered Mesh Upper\n• Waffle-inspired Rubber Outsole for Excellent Traction\n• Weight: Approx 288g (Men size 10)\n• Ideal for Daily Road Running & Fitness Training',
                'topsale'          => 0,
                'feature_product'  => 1,
                'flashsale'        => 0,
                'is_new'           => 1,
                'is_prime'         => 0,
                'status'           => 1,
                'image'            => 'uploads/products/nike_pegasus40.png',
            ],
            [
                'name'             => 'Philips Air Fryer XXL 7000 Series (8.3L Capacity)',
                'slug'             => 'philips-air-fryer-xxl-7000-series-8-3l',
                'category_id'      => $catHome->id,
                'brand_id'         => $brandPhilips->id,
                'product_code'     => 'PH-AF-7000',
                'purchase_price'   => 20000,
                'old_price'        => 32000,
                'new_price'        => 27900,
                'stock'            => 15,
                'sold'             => 11,
                'meta_description' => 'Philips Air Fryer XXL 7000 Series 8.3L with Rapid CombiAir technology and NutriU smart recipe app.',
                'description'      => 'Cook healthier, delicious meals with up to 90% less oil using Philips Air Fryer XXL. Features Rapid CombiAir technology that adjusts airflow for crispy on the outside, tender on the inside results. Comes with built-in Food Thermometer probe.',
                'features'         => '• 8.3 Litre XXL Capacity (Cooks up to 2kg or a full whole chicken)\n• Rapid CombiAir & Dynamic Airflow Technology\n• 22 Cooking Preset Functions (Air fry, Bake, Roast, Grill, Dehydrate, Defrost)\n• Integrated Digital Food Thermometer Probe\n• WiFi Connected with NutriU Recipe App Guidance\n• Dishwasher-Safe QuickClean Basket & Drawer',
                'topsale'          => 1,
                'feature_product'  => 0,
                'flashsale'        => 1,
                'is_new'           => 0,
                'is_prime'         => 1,
                'status'           => 1,
                'image'            => 'uploads/products/philips_airfryer.png',
            ],
        ];

        foreach ($productsData as $pData) {
            $imagePath = $pData['image'];
            unset($pData['image']);

            $product = Product::updateOrCreate(['slug' => $pData['slug']], $pData);

            Productimage::firstOrCreate([
                'product_id' => $product->id,
            ], [
                'image' => $imagePath,
            ]);

            $this->info("✓ Seeded Product: {$product->name} (BDT {$product->new_price})");
        }

        $this->info('🌱 Seeding Knowledge Base Policy Articles...');

        $articles = [
            [
                'title'       => 'Delivery & Shipping Policy',
                'category'    => 'shipping',
                'content'     => "We offer fast nationwide delivery across Bangladesh.\n• Inside Dhaka City: Delivered within 24-48 hours (Delivery Fee: 60 BDT).\n• Outside Dhaka: Delivered within 2-4 business days via Steadfast or Pathao Courier (Delivery Fee: 120 BDT).\n• Express Same-Day Delivery is available inside Dhaka for urgent orders placed before 12 PM.\n• Cash on Delivery (COD) is supported nationwide.",
                'is_published'=> 1,
            ],
            [
                'title'       => '7-Day Return & Replacement Policy',
                'category'    => 'returns',
                'content'     => "Customer satisfaction is our top priority. We offer a hassle-free 7-day replacement policy under the following conditions:\n• Product is damaged, defective, or physically broken upon unboxing (Video proof required).\n• Wrong product, size, or color was delivered.\n• Product does not match description on website.\n• To initiate a return, contact our support team or initiate chat with conversation ID.",
                'is_published'=> 1,
            ],
            [
                'title'       => 'Refund Policy & Processing Time',
                'category'    => 'refunds',
                'content'     => "Refunds are issued under the following terms:\n• If a product is out of stock after order confirmation, a 100% full refund is issued immediately.\n• Returned products undergo quality inspection (takes 24-48 hours).\n• Refunds are processed via original payment channel (bKash, Nagad, Card, Bank Transfer) within 3-5 business days after inspection approval.",
                'is_published'=> 1,
            ],
            [
                'title'       => 'Order Cancellation Policy',
                'category'    => 'cancellation',
                'content'     => "• Customers can cancel their order free of charge before the item has been dispatched/shipped.\n• Once an order status is marked as 'Dispatched' or 'In Transit', cancellations are subject to courier delivery charge deduction.\n• To cancel an active order, please inform our support agent or click Cancel in your customer profile dashboard.",
                'is_published'=> 1,
            ],
            [
                'title'       => 'Payment Methods & bKash Cashback',
                'category'    => 'payment',
                'content'     => "We accept all popular Bangladesh payment methods:\n• Cash on Delivery (COD)\n• bKash Payment (Merchant Gateway)\n• Nagad & Rocket Mobile Banking\n• Visa, Mastercard & AMEX Debit/Credit Cards\n• Bank Wire Transfer",
                'is_published'=> 1,
            ],
        ];

        foreach ($articles as $art) {
            KnowledgeArticle::updateOrCreate(['title' => $art['title']], $art);
            $this->info("✓ Seeded Policy Article: {$art['title']}");
        }

        $this->info('🎉 All Products, Categories, Brands & Knowledge Articles Seeded Successfully!');
        return 0;
    }
}
