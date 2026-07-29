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
       $shurjopay = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        if ($shurjopay) {

            Config::set(['shurjopay.apiCredentials.username' => $shurjopay->username]);
            Config::set(['shurjopay.apiCredentials.password' => $shurjopay->password]);
            Config::set(['shurjopay.apiCredentials.prefix' => $shurjopay->prefix]);
            Config::set(['shurjopay.apiCredentials.return_url' => $shurjopay->success_url]);
            Config::set(['shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url]);
            Config::set(['shurjopay.apiCredentials.base_url' => $shurjopay->base_url]);
        }
        $generalsetting = GeneralSetting::where('status',1)->limit(1)->first();
        // Strip 'public/' prefix from image paths so asset() works everywhere
        if ($generalsetting) {
            foreach (['white_logo', 'dark_logo', 'favicon', 'og_baner'] as $field) {
                if ($generalsetting->$field && str_starts_with($generalsetting->$field, 'public/')) {
                    $generalsetting->$field = substr($generalsetting->$field, 7);
                }
            }
        }
        view()->share('generalsetting',$generalsetting);

        $sidecategories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','slug','status','image')->get();
        view()->share('sidecategories',$sidecategories);

        $menucategories = Category::where('status',1)->select('id','name','slug','status','image')->get();
        view()->share('menucategories',$menucategories);

        $contact = Contact::where('status',1)->first();
        view()->share('contact',$contact);

        $socialicons = SocialMedia::where('status',1)->get();
        view()->share('socialicons',$socialicons);

        $pages = CreatePage::where('status',1)->limit(3)->get();
        view()->share('pages',$pages);

        $pagesright = CreatePage::where('status',1)->skip(1)->limit(5)->get();
        view()->share('pagesright',$pagesright);

        $cmnmenu = CreatePage::where('status',1)->get();
        view()->share('cmnmenu',$cmnmenu);

        $brands = Brand::where('status',1)->get();
        view()->share('brands',$brands);

        $notices = Notice::where('status',1)->orderBy('order_id','ASC')->orderBy('id','ASC')->get();
        view()->share('notices',$notices);

        $stories = Story::where('status',1)->orderBy('order_id','ASC')->orderBy('id','ASC')->with('product', 'product.image')->get();
        view()->share('stories',$stories);

        $neworder = Order::where('order_status','1')->count();
        view()->share('neworder',$neworder);

        $pendingorder = Order::where('order_status','1')->latest()->limit(9)->get();
        view()->share('pendingorder',$pendingorder);

        $orderstatus = OrderStatus::get();
        view()->share('orderstatus',$orderstatus);

        $pixels = EcomPixel::where('status',1)->get();
        view()->share('pixels',$pixels);

        $gtm_code = GoogleTagManager::where('status',1)->get();
        view()->share('gtm_code',$gtm_code);
    }
}
