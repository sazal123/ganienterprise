<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\OrderStatus;
use App\Models\EcomPixel;
use App\Models\GoogleTagManager;
use App\Models\Notice;
use App\Models\Story;
use App\Models\Order;
use App\Models\PaymentGateway;
use Config;
use Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Detect cPanel public_html or custom DOCUMENT_ROOT deployment
        $this->app->bind('path.public', function () {
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT']) && (file_exists($_SERVER['DOCUMENT_ROOT'] . '/index.php') || is_dir($_SERVER['DOCUMENT_ROOT'] . '/uploads'))) {
                return $_SERVER['DOCUMENT_ROOT'];
            } elseif (is_dir(base_path('../public_html'))) {
                return base_path('../public_html');
            }
            return base_path('public');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\RateLimiter::for('chat-messages', function (\Illuminate\Http\Request $request) {
            $key = auth('customer')->id() 
                ?: ($request->header('X-Guest-Token') 
                ?: ($request->cookie(\App\Services\GuestTokenService::COOKIE_NAME) ?: $request->ip()));

            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($key);
        });
        if (\Illuminate\Support\Facades\Schema::hasTable('payment_gateways') && \Illuminate\Support\Facades\Schema::hasColumn('payment_gateways', 'status')) {
            $shurjopay = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
            if ($shurjopay) {
                Config::set(['shurjopay.apiCredentials.username' => $shurjopay->username]);
                Config::set(['shurjopay.apiCredentials.password' => $shurjopay->password]);
                Config::set(['shurjopay.apiCredentials.prefix' => $shurjopay->prefix]);
                Config::set(['shurjopay.apiCredentials.return_url' => $shurjopay->success_url]);
                Config::set(['shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url]);
                Config::set(['shurjopay.apiCredentials.base_url' => $shurjopay->base_url]);
            }
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('general_settings')) {
            $generalsetting = GeneralSetting::where('status',1)->limit(1)->first() ?? new GeneralSetting([
                'name' => 'Store',
                'white_logo' => '',
                'dark_logo' => '',
                'favicon' => '',
                'header_code' => '',
                'footer_code' => '',
                'meta_title' => '',
                'meta_description' => '',
                'copyright' => '',
                'facebook_verification' => '',
                'google_verification' => '',
            ]);
            if ($generalsetting) {
                foreach (['white_logo', 'dark_logo', 'favicon', 'og_baner'] as $field) {
                    if ($generalsetting->$field && str_starts_with($generalsetting->$field, 'public/')) {
                        $generalsetting->$field = substr($generalsetting->$field, 7);
                    }
                }
            }
            view()->share('generalsetting',$generalsetting);

            if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                $sidecategories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','slug','status','image')->get();
                view()->share('sidecategories',$sidecategories);

                $menucategories = Category::where('status',1)->select('id','name','slug','status','image')->get();
                view()->share('menucategories',$menucategories);
            } else {
                view()->share('sidecategories', collect([]));
                view()->share('menucategories', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('contacts')) {
                $contact = Contact::where('status',1)->first() ?? new Contact();
                view()->share('contact',$contact);
            } else {
                view()->share('contact', new Contact());
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('social_media')) {
                $socialicons = SocialMedia::where('status',1)->get();
                view()->share('socialicons',$socialicons);
            } else {
                view()->share('socialicons', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('create_pages')) {
                $pages = CreatePage::where('status',1)->limit(3)->get();
                view()->share('pages',$pages);

                $pagesright = CreatePage::where('status',1)->skip(1)->limit(5)->get();
                view()->share('pagesright',$pagesright);

                $cmnmenu = CreatePage::where('status',1)->get();
                view()->share('cmnmenu',$cmnmenu);
            } else {
                view()->share('pages', collect([]));
                view()->share('pagesright', collect([]));
                view()->share('cmnmenu', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('brands')) {
                $brands = Brand::where('status',1)->get();
                view()->share('brands',$brands);
            } else {
                view()->share('brands', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('notices')) {
                $notices = Notice::where('status',1)->orderBy('order_id','ASC')->orderBy('id','ASC')->get();
                view()->share('notices',$notices);
            } else {
                view()->share('notices', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('stories')) {
                $stories = Story::where('status',1)->orderBy('order_id','ASC')->orderBy('id','ASC')->with('product', 'product.image')->get();
                view()->share('stories',$stories);
            } else {
                view()->share('stories', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('orders')) {
                $neworder = Order::where('order_status','1')->count();
                view()->share('neworder',$neworder);

                $pendingorder = Order::where('order_status','1')->latest()->limit(9)->get();
                view()->share('pendingorder',$pendingorder);
            } else {
                view()->share('neworder', 0);
                view()->share('pendingorder', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('order_statuses')) {
                $orderstatus = OrderStatus::get();
                view()->share('orderstatus',$orderstatus);
            } else {
                view()->share('orderstatus', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('ecom_pixels')) {
                $pixels = EcomPixel::where('status',1)->get();
                view()->share('pixels',$pixels);
            } else {
                view()->share('pixels', collect([]));
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('google_tag_managers')) {
                $gtm_code = GoogleTagManager::where('status',1)->get();
                view()->share('gtm_code',$gtm_code);
            } else {
                view()->share('gtm_code', collect([]));
            }
        }
    }
}
