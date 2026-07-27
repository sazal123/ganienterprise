<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\District;
use App\Models\CreatePage;
use App\Models\Campaign;
use App\Models\Banner;
use App\Models\BannerCategory;
use App\Models\ShippingCharge;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Review;
use App\Models\Contact;
use App\Models\GeneralSetting;
use Session;
use Cart;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class FrontendController extends Controller
{
    public function index()
    {
        $generalsetting = GeneralSetting::where('status',1)->limit(1)->first();

        $frontcategory = Category::where(['status' => 1])
            ->select('id', 'name', 'image', 'slug', 'status')
            ->get();

        // Hero Sliders — try "Hero Slider" category first, fallback to "Slider" category
        $heroCat = \App\Models\BannerCategory::where('name', 'Hero Slider')->value('id');
        $sliderCatId = $heroCat ?: 1;
        $sliders = Banner::where(['status' => 1, 'category_id' => $sliderCatId])
            ->select('id', 'image', 'link', 'title', 'subtitle', 'btn_text')
            ->get();

        // Campaign ads (category_id = 7)
        $campaognads = Banner::where(['status' => 1, 'category_id' => 7])
            ->select('id', 'image', 'link')
            ->limit(1)
            ->get();

        // Slider bottom ads (category_id = 5)
        $sliderbottomads = Banner::where(['status' => 1, 'category_id' => 5])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        // Footer top ads (category_id = 6)
        $footertopads = Banner::where(['status' => 1, 'category_id' => 6])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        // Flash sale products
        $flas_sales = Product::where(['status' => 1, 'flashsale' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','sold','stock')
            ->with('image', 'prosizes', 'procolors')
            ->limit(12)
            ->get();

        // Trending Products (topsale = hot deals / trending)
        $trendingProducts = Product::where(['status' => 1, 'topsale' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'images', 'procolors')
            ->limit(8)
            ->get();

        // New Collection (is_new flag)
        $newProducts = Product::where(['status' => 1, 'is_new' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'images', 'procolors')
            ->limit(5)
            ->get();

        // Prime Collection (is_prime flag)
        $primeProducts = Product::where(['status' => 1, 'is_prime' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'images', 'procolors')
            ->limit(5)
            ->get();

        // Top Category Products (featured products)
        $topCategoryProducts = Product::where(['status' => 1, 'feature_product' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'images', 'procolors')
            ->limit(8)
            ->get();

        // Hot deals (for backward compatibility)
        $hotdeal_top = $trendingProducts;
        $hotdeal_bottom = Product::where(['status' => 1, 'topsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->skip(8)
            ->limit(12)
            ->get();

        // Category-wise products
        if($generalsetting->show_category_wise_products){
            $homeproducts = Category::where(['front_view' => 1, 'status' => 1])
                ->orderBy('id', 'ASC')
                ->with(['products', 'products.image', 'products.prosize', 'products.procolor'])
                ->get()
                ->map(function ($query) {
                    $query->setRelation('products', $query->products->take(12));
                    return $query;
                });
        }else{
            $homeproducts = null;
        }

        // Customer Reviews (banners with category_id = 8)
        $reviews = Banner::where(['status' => 1, 'category_id' => 8])
            ->select('id', 'image', 'link')
            ->limit(3)
            ->get();

        // All products
        if($generalsetting->show_all_products){
            $all_products = Product::where(['status' => 1])
                ->inRandomOrder()
                ->select('id', 'name', 'slug', 'new_price', 'old_price','sold','stock')
                ->with('image', 'images', 'prosizes', 'procolors')
                ->limit(30)
                ->get();
        }else{
            $all_products = null;
        }

        // All active categories for category grid (front_view enabled)
        $homeCategories = Category::where(['status' => 1, 'front_view' => 1])
            ->select('id', 'name', 'slug', 'image')
            ->get();

        // Clothing spotlight categories
        $spotlightCategories = Category::where(['status' => 1, 'spotlight' => 1])
            ->select('id', 'name', 'slug', 'image')
            ->get();

        // Prime drop banner — look up "Prime Drop Banner" category dynamically
        $primeDropCatId = BannerCategory::where('name', 'Prime Drop Banner')->value('id');
        $primeDropBanner = null;
        $primeDropProducts = collect();
        if ($primeDropCatId) {
            $primeDropBanner = Banner::where(['status' => 1, 'category_id' => $primeDropCatId])
                ->select('id', 'image', 'link', 'title', 'subtitle', 'btn_text')
                ->first();
            // Products to showcase on the right side of the banner
            $primeDropProducts = Product::where(['status' => 1])
                ->inRandomOrder()
                ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
                ->with('image')
                ->limit(3)
                ->get();
        }

        return view('frontEnd.layouts.pages.index', compact(
            'sliders', 'frontcategory', 'trendingProducts', 'newProducts', 'primeProducts', 'topCategoryProducts',
            'hotdeal_top', 'hotdeal_bottom', 'homeproducts', 'sliderbottomads', 'footertopads',
            'flas_sales', 'campaognads', 'reviews', 'all_products', 'homeCategories',
            'primeDropBanner', 'primeDropProducts', 'spotlightCategories'
        ));
    }

    public function hotdeals(Request $request)
    {

        $products = Product::where(['status' => 1, 'topsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock');
        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.hotdeals', compact('products'));
    }
    public function shop(Request $request)
    {
        // Get global price range (unfiltered)
        $globalMin = Product::where('status', 1)->min('new_price');
        $globalMax = Product::where('status', 1)->max('new_price');

        $products = Product::where(['status' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'images', 'procolors', 'prosizes');

        // Filter by category if specified
        if ($request->category_filter) {
            $products = $products->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_filter);
            });
        }

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price)
                                 ->where('new_price', '<=', $request->max_price);
        }

        $products = $products->paginate(20);

        // Categories for sidebar filter
        $categories = Category::where('status', 1)
            ->with(['subcategories' => function ($q) {
                $q->where('status', 1)->select('id', 'slug', 'subcategoryName', 'category_id');
            }])
            ->select('id', 'name', 'slug')
            ->get();

        return view('frontEnd.layouts.pages.shop', compact(
            'products', 'categories', 'globalMin', 'globalMax'
        ));
    }

    public function collectionPrime(Request $request)
    {
        $products = Product::where(['status' => 1, 'is_prime' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'procolors');
        if ($request->sort == 1) { $products = $products->orderBy('created_at', 'desc'); }
        elseif ($request->sort == 2) { $products = $products->orderBy('created_at', 'asc'); }
        elseif ($request->sort == 3) { $products = $products->orderBy('new_price', 'desc'); }
        elseif ($request->sort == 4) { $products = $products->orderBy('new_price', 'asc'); }
        else { $products = $products->latest(); }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.shop', compact('products'));
    }

    public function collectionNew(Request $request)
    {
        $products = Product::where(['status' => 1, 'is_new' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->with('image', 'procolors');
        if ($request->sort == 1) { $products = $products->orderBy('created_at', 'desc'); }
        elseif ($request->sort == 2) { $products = $products->orderBy('created_at', 'asc'); }
        elseif ($request->sort == 3) { $products = $products->orderBy('new_price', 'desc'); }
        elseif ($request->sort == 4) { $products = $products->orderBy('new_price', 'asc'); }
        else { $products = $products->latest(); }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.shop', compact('products'));
    }

    public function flashsales(Request $request)
    {

        $products = Product::where(['status' => 1, 'flashsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price','stock');
        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }
        $products = $products->paginate(36);
        return view('frontEnd.layouts.pages.flashsales', compact('products'));
    }

    public function category($slug, Request $request)
    {
        $soldShow = $request->sold == 'show' ? true : false;
        $category = Category::where(['slug' => $slug, 'status' => 1])->firstOrFail();

        // Global price range for slider
        $globalMin = Product::where(['status' => 1, 'category_id' => $category->id])->min('new_price');
        $globalMax = Product::where(['status' => 1, 'category_id' => $category->id])->max('new_price');

        $products = Product::where(['status' => 1, 'category_id' => $category->id])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'sold', 'stock')
            ->with('image', 'images', 'procolors', 'prosizes');

        $subcategories = Subcategory::where('category_id', $category->id)->where('status', 1)->get();

        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price)
                                 ->where('new_price', '<=', $request->max_price);
        }

        $selectedSubcategories = $request->input('subcategory', []);
        $products = $products->when($selectedSubcategories, function ($query) use ($selectedSubcategories) {
            return $query->whereHas('subcategory', function ($subQuery) use ($selectedSubcategories) {
                $subQuery->whereIn('id', $selectedSubcategories);
            });
        });

        $products = $products->paginate(24);

        // Categories for sidebar
        $categories = Category::where('status', 1)
            ->with(['subcategories' => function ($q) {
                $q->where('status', 1)->select('id', 'slug', 'subcategoryName', 'category_id');
            }])
            ->select('id', 'name', 'slug')
            ->get();

        return view('frontEnd.layouts.pages.category', compact(
            'category', 'products', 'subcategories',
            'globalMin', 'globalMax', 'soldShow', 'categories'
        ));
    }

    public function subcategory($slug, Request $request)
    {
        $soldShow = $request->sold=='show'?true:false;
        $subcategory = Subcategory::where(['slug' => $slug, 'status' => 1])->first();
        $products = Product::where(['status' => 1, 'subcategory_id' => $subcategory->id])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id','sold','stock');
        $childcategories = Childcategory::where('subcategory_id', $subcategory->id)->get();

        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }

        $selectedChildcategories = $request->input('childcategory', []);
        $products = $products->when($selectedChildcategories, function ($query) use ($selectedChildcategories) {
            return $query->whereHas('childcategory', function ($subQuery) use ($selectedChildcategories) {
                $subQuery->whereIn('id', $selectedChildcategories);
            });
        });

        $products = $products->paginate(24);
        // return $products;
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug')
            ->get();

        return view('frontEnd.layouts.pages.subcategory', compact('subcategory', 'products', 'impproducts', 'childcategories', 'max_price', 'min_price','soldShow'));
    }

    public function products($slug, Request $request)
    {
        $soldShow = $request->sold=='show'?true:false;
        $childcategory = Childcategory::where(['slug' => $slug, 'status' => 1])->first();
        $childcategories = Childcategory::where('subcategory_id', $childcategory->subcategory_id)->get();
        $products = Product::where(['status' => 1, 'childcategory_id' => $childcategory->id])->with('category')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id', 'childcategory_id','sold','stock');


        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if($request->min_price && $request->max_price){
            $products = $products->where('new_price','>=',$request->min_price);
            $products = $products->where('new_price','<=',$request->max_price);
        }

        $products = $products->paginate(24);
        // return $products;
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug','stock')
            ->get();

        return view('frontEnd.layouts.pages.childcategory', compact('childcategory', 'products', 'impproducts', 'min_price', 'max_price', 'childcategories','soldShow'));
    }


    public function details($slug)
    {
        $details = Product::where(['slug' => $slug, 'status' => 1])
            ->with('image', 'mainImages', 'images', 'category', 'subcategory', 'childcategory', 'brand')
            ->firstOrFail();

        // Related products from same category
        $relatedProducts = Product::where(['category_id' => $details->category_id, 'status' => 1])
            ->where('id', '!=', $details->id)
            ->with('image', 'procolors', 'prosizes')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->limit(12)
            ->get();

        // "Pair It & Shine!" — also fetch some unrelated products for variety
        $pairProducts = Product::where('status', 1)
            ->where('id', '!=', $details->id)
            ->where('category_id', '!=', $details->category_id)
            ->with('image')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $reviews = Review::where('product_id', $details->id)->get();

        // Review stats
        $avgRating = $reviews->avg('ratting');
        $totalReviews = $reviews->count();

        $productcolors = Productcolor::where('product_id', $details->id)
            ->with('color')
            ->get();

        $productsizes = Productsize::where('product_id', $details->id)
            ->with('size')
            ->get();

        // Parse features (pipe-separated from admin)
        $features = [];
        if ($details->features) {
            $featureItems = explode('|', $details->features);
            foreach ($featureItems as $item) {
                $item = trim($item);
                if ($item) {
                    $features[] = $item;
                }
            }
        }

        return view('frontEnd.layouts.pages.details', compact(
            'details', 'relatedProducts', 'pairProducts', 'shippingcharge',
            'productcolors', 'productsizes', 'reviews', 'avgRating', 'totalReviews', 'features'
        ));
    }
    public function quickview(Request $request)
    {
        $data['data'] = Product::where(['id' => $request->id, 'status' => 1])->with('images')->withCount('reviews')->first();
        $data = view('frontEnd.layouts.ajax.quickview', $data)->render();
        if ($data != '') {
            echo $data;
        }
    }
    public function livesearch(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->get();

        if (empty($request->category) && empty($request->keyword)) {
            $products = [];
        }
        return view('frontEnd.layouts.ajax.search', compact('products'));
    }
    public function search(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price','stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->paginate(36);
        $keyword = $request->keyword;
        return view('frontEnd.layouts.pages.search', compact('products', 'keyword'));
    }

    public function shipping_charge(Request $request)
    {

        $shipping = ShippingCharge::where(['id' => $request->id])->first();
        Session::put('shipping', $shipping->amount);
        return view('frontEnd.layouts.ajax.cart');
    }

    public function contact(Request $request)
    {
        // Check if form data is present
        if ($request->has(['name', 'phone', 'email', 'subject', 'message'])) {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|numeric',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // Prepare data for email
            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];


            // Send email
            $contact = Contact::where('status',1)->first();
            if($contact->email){
                try {
                    Mail::to($contact->email)->send(new ContactMail($data));
                } catch (Exception $e) {
                    // Log the exception message
                    Log::error('Email sending failed: ' . $e->getMessage());
                }
            }

            // Redirect to the same page with a success message in query parameters
            return redirect()->route('contact')->with('success', 'Your message has been sent successfully!');
        }

        // Load the contact form view with any success message
        return view('frontEnd.layouts.pages.contact');
    }

    public function page($slug)
    {
        $page = CreatePage::where('slug', $slug)->firstOrFail();
        return view('frontEnd.layouts.pages.page', compact('page'));
    }
    public function districts(Request $request)
    {
        $areas = District::where(['district' => $request->id])->pluck('area_name', 'id');
        return response()->json($areas);
    }
    public function campaign(Request $request, $slug)
    {
        $campaign_data = Campaign::where('slug', $slug)->with(['images', 'categories'])->firstOrFail();

        // Get all category IDs attached to this campaign
        $campaignCategoryIds = $campaign_data->categories->pluck('id')->toArray();
        if ($campaign_data->category_id && !in_array($campaign_data->category_id, $campaignCategoryIds)) {
            $campaignCategoryIds[] = $campaign_data->category_id;
        }

        // Get product IDs explicitly attached to this campaign
        $productIds = DB::table('campaign_product')
            ->where('campaign_id', $campaign_data->id)
            ->pluck('product_id')
            ->toArray();

        // Include all products belonging to any of the selected categories
        if (!empty($campaignCategoryIds)) {
            $catProductIds = Product::whereIn('category_id', $campaignCategoryIds)->where('status', 1)->pluck('id')->toArray();
            $productIds = array_merge($productIds, $catProductIds);
        }

        if ($campaign_data->product_id) {
            $productIds[] = $campaign_data->product_id;
        }

        $productIds = array_unique(array_filter($productIds));

        $query = Product::where('status', 1);

        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        }

        // Specific category tab filter
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Live Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Sorting
        if ($request->filled('sort')) {
            if ($request->sort === 'price_low') {
                $query->orderBy('new_price', 'asc');
            } elseif ($request->sort === 'price_high') {
                $query->orderBy('new_price', 'desc');
            } elseif ($request->sort === 'oldest') {
                $query->orderBy('id', 'asc');
            } else {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->with(['image', 'sizes', 'colors'])->paginate(12)->withQueryString();

        // Fallback to showcase all active products if campaign has no attached items or categories
        if ($products->total() == 0 && empty($productIds)) {
            $products = Product::where('status', 1)->with(['image', 'sizes', 'colors'])->paginate(12)->withQueryString();
        }

        // Categories to display as tabs on the view page
        if (!empty($campaignCategoryIds)) {
            $categories = Category::whereIn('id', $campaignCategoryIds)->where('status', 1)->get();
        } elseif (!empty($productIds)) {
            $categories = Category::whereIn('id', function($q) use ($productIds) {
                $q->select('category_id')->from('products')->whereIn('id', $productIds)->whereNotNull('category_id');
            })->get();
        } else {
            $categories = Category::where('status', 1)->take(8)->get();
        }

        $shippingcharge = ShippingCharge::where('status', 1)->get();

        if ($request->ajax()) {
            $html = view('frontEnd.layouts.pages.campaign._campaign_product_grid', compact('products'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'total' => $products->total()
            ]);
        }

        return view('frontEnd.layouts.pages.campaign.campaign', compact('campaign_data', 'products', 'categories', 'shippingcharge'));
    }

    public function payment_success(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        if ($data[0]->sp_code != 1000) {
            Toastr::error('Your payment failed, try again', 'Oops!');
            if ($data[0]->value1 == 'customer_payment') {
                return redirect()->route('home');
            } else {
                return redirect()->route('home');
            }
        }

        if ($data[0]->value1 == 'customer_payment') {

            $customer = Customer::find(Auth::guard('customer')->user()->id);

            // order data save
            $order = new Order();
            $order->invoice_id = $data[0]->id;
            $order->amount = $data[0]->amount;
            $order->customer_id = Auth::guard('customer')->user()->id;
            $order->order_status = $data[0]->bank_status;
            $order->save();

            // payment data save
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->customer_id = Auth::guard('customer')->user()->id;
            $payment->payment_method = 'shurjopay';
            $payment->amount = $order->amount;
            $payment->trx_id = $data[0]->bank_trx_id;
            $payment->sender_number = $data[0]->phone_no;
            $payment->payment_status = 'paid';
            $payment->save();
            // order details data save
            foreach (Cart::instance('shopping')->content() as $cart) {
                $order_details = new OrderDetails();
                $order_details->order_id = $order->id;
                $order_details->product_id = $cart->id;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->save();
            }

            Cart::instance('shopping')->destroy();
            Toastr::error('Thanks, Your payment send successfully', 'Success!');
            return redirect()->route('home');
        }

        Toastr::error('Something wrong, please try agian', 'Error!');
        return redirect()->route('home');
    }
    public function payment_cancel(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        Toastr::error('Your payment cancelled', 'Cancelled!');
        if ($data[0]->sp_code != 1000) {
            if ($data[0]->value1 == 'customer_payment') {
                return redirect()->route('home');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function offers()
    {
        return view('frontEnd.layouts.pages.offers');
    }

    public function schoolBagsLanding(Request $request)
    {
        $query = Product::where('status', 1);

        // Broad matching for school bags / backpacks / bags
        $query->where(function($q) {
            $q->where('name', 'LIKE', '%bag%')
              ->orWhere('name', 'LIKE', '%backpack%')
              ->orWhere('name', 'LIKE', '%school%')
              ->orWhereHas('category', function($catQ) {
                  $catQ->where('name', 'LIKE', '%bag%')
                       ->orWhere('name', 'LIKE', '%school%');
              });
        });

        // Persona / Grade tab filter
        if ($request->filled('persona')) {
            $persona = $request->persona;
            if ($persona === 'kids') {
                $query->where(function($q) {
                    $q->where('name', 'LIKE', '%kid%')
                      ->orWhere('name', 'LIKE', '%preschool%')
                      ->orWhere('name', 'LIKE', '%small%')
                      ->orWhere('name', 'LIKE', '%cartoon%')
                      ->orWhere('name', 'LIKE', '%cute%')
                      ->orWhere('name', 'LIKE', '%baby%');
                });
            } elseif ($persona === 'primary') {
                $query->where(function($q) {
                    $q->where('name', 'LIKE', '%primary%')
                      ->orWhere('name', 'LIKE', '%class%')
                      ->orWhere('name', 'LIKE', '%medium%')
                      ->orWhere('name', 'LIKE', '%school%');
                });
            } elseif ($persona === 'high') {
                $query->where(function($q) {
                    $q->where('name', 'LIKE', '%college%')
                      ->orWhere('name', 'LIKE', '%laptop%')
                      ->orWhere('name', 'LIKE', '%large%')
                      ->orWhere('name', 'LIKE', '%travel%');
                });
            } elseif ($persona === 'trolley') {
                $query->where(function($q) {
                    $q->where('name', 'LIKE', '%trolley%')
                      ->orWhere('name', 'LIKE', '%wheel%')
                      ->orWhere('name', 'LIKE', '%roll%');
                });
            }
        }

        // Live Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Price Sort
        if ($request->filled('sort')) {
            if ($request->sort === 'price_low') {
                $query->orderBy('new_price', 'asc');
            } elseif ($request->sort === 'price_high') {
                $query->orderBy('new_price', 'desc');
            } elseif ($request->sort === 'oldest') {
                $query->orderBy('id', 'asc');
            } else {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->with(['image', 'sizes', 'colors'])->paginate(12)->withQueryString();

        // If no bag matches, fallback to all active products to ensure landing page always showcases items nicely
        if ($products->total() == 0) {
            $products = Product::where('status', 1)->with(['image', 'sizes', 'colors'])->paginate(12)->withQueryString();
        }

        // Top 4 spotlight products
        $spotlightProducts = Product::where('status', 1)
            ->with(['image', 'sizes', 'colors'])
            ->latest()
            ->take(4)
            ->get();

        $shipping_charge = ShippingCharge::where('status', 1)->get();

        if ($request->ajax()) {
            $html = view('frontEnd.layouts.pages._school_bag_grid', compact('products'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'total' => $products->total()
            ]);
        }

        return view('frontEnd.layouts.pages.school_bag_landing', compact('products', 'spotlightProducts', 'shipping_charge'));
    }

}
