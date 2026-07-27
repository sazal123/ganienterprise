<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\CampaignReview;
use App\Models\Campaign;
use Image;
use Toastr;
use Str;
use File;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $show_data = Campaign::orderBy('id','DESC')->get();
        return view('backEnd.campaign.index',compact('show_data'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->select('id', 'name')->get();
        $products = Product::where(['status'=>1])->select('id','name','status','category_id')->get();
        return view('backEnd.campaign.create',compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'short_description' => 'nullable',
            'description' => 'nullable',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_one' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_three' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'category_id' => 'nullable',
            'product_id' => 'nullable|array',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'review' => 'nullable',
            'deadline' => 'nullable',
            'top_title_1' => 'nullable|string|max:255',
            'top_title_2' => 'nullable|string|max:255',
            'heading_1' => 'nullable|string|max:255',
            'feature_1' => 'nullable|string|max:255',
            'feature_2' => 'nullable|string|max:255',
            'heading_2' => 'nullable|string|max:255',
            'heading_3' => 'nullable|string|max:255',
            'heading_4' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'billing_details' => 'nullable|string|max:255',
        ]);

        $categoryIds = [];
        if ($request->has('category_id')) {
            $categoryIds = is_array($request->category_id) ? array_filter($request->category_id) : [$request->category_id];
        }

        // Resolve product IDs (from explicit product_id array + category products if selected)
        $allProductIds = [];
        if (!empty($categoryIds)) {
            $catProductIds = Product::whereIn('category_id', $categoryIds)->where('status', 1)->pluck('id')->toArray();
            $allProductIds = array_merge($allProductIds, $catProductIds);
        }
        if ($request->has('product_id') && is_array($request->product_id)) {
            $allProductIds = array_merge($allProductIds, $request->product_id);
        }
        $allProductIds = array_unique(array_filter($allProductIds));

        // Prepare input data
        $input = $request->except('image', 'product_id', 'category_id');
        $input['status'] = true;
        $input['category_id'] = !empty($categoryIds) ? reset($categoryIds) : null;
        $input['product_id'] = !empty($allProductIds) ? reset($allProductIds) : null;

        // Handle Banner Image
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time() . '-' . strtolower(preg_replace('/\s+/', '-', $banner->getClientOriginalName()));
            $uploadPath = 'public/uploads/campaign/';
            $bannerUrl = $uploadPath . $bannerName;
            $banner->move($uploadPath, $bannerName);
            $input['banner'] = $bannerUrl;
        }

        // Handle Image One
        if ($request->hasFile('image_one')) {
            $image_one = $request->file('image_one');
            $name1 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_one->getClientOriginalName()));
            $name1 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name1);
            $uploadPath1 = 'public/uploads/campaign/';
            $imageUrl1 = $uploadPath1 . $name1;

            $img1 = Image::make($image_one->getRealPath());
            $img1->encode('webp', 90);
            $img1->save($imageUrl1);
            $input['image_one'] = $imageUrl1;
        }

        // Handle Image Two
        if ($request->hasFile('image_two')) {
            $image_two = $request->file('image_two');
            $name2 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_two->getClientOriginalName()));
            $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name2);
            $uploadPath2 = 'public/uploads/campaign/';
            $imageUrl2 = $uploadPath2 . $name2;

            $img2 = Image::make($image_two->getRealPath());
            $img2->encode('webp', 90);
            $img2->save($imageUrl2);
            $input['image_two'] = $imageUrl2;
        }

        // Handle Image Three
        if ($request->hasFile('image_three')) {
            $image_three = $request->file('image_three');
            $name3 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_three->getClientOriginalName()));
            $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name3);
            $uploadPath3 = 'public/uploads/campaign/';
            $imageUrl3 = $uploadPath3 . $name3;

            $img3 = Image::make($image_three->getRealPath());
            $img3->encode('webp', 90);
            $img3->save($imageUrl3);
            $input['image_three'] = $imageUrl3;
        }

        // Create slug & video ID
        $input['slug'] = strtolower(Str::slug($request->name));
        $input['video'] = $this->getYouTubeVideoId($request->video);

        // Create campaign
        $campaign = Campaign::create($input);

        // Sync categories to pivot table
        if (!empty($categoryIds)) {
            $campaign->categories()->sync($categoryIds);
        }

        // Sync products to pivot table
        if (!empty($allProductIds)) {
            $campaign->products()->sync($allProductIds);
        }

        // Handle review images
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $name = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image->getClientOriginalName()));
                $uploadPath = 'public/uploads/campaign/';
                $image->move($uploadPath, $name);
                $imageUrl = $uploadPath . $name;

                $pimage = new CampaignReview();
                $pimage->campaign_id = $campaign->id;
                $pimage->image = $imageUrl;
                $pimage->save();
            }
        }

        Toastr::success('Success', 'Campaign created successfully');
        return redirect()->route('campaign.index');
    }

    public function edit($id)
    {
        $edit_data = Campaign::with(['images', 'categories'])->findOrFail($id);

        $select_products = DB::select('
            SELECT products.id, products.name, products.status 
            FROM products
            INNER JOIN campaign_product ON products.id = campaign_product.product_id
            WHERE campaign_product.campaign_id = ?
        ', [$id]);

        $select_product_ids = array_column($select_products, 'id');
        if ($edit_data->product_id && !in_array($edit_data->product_id, $select_product_ids)) {
            $select_product_ids[] = $edit_data->product_id;
        }

        $select_category_ids = $edit_data->categories->pluck('id')->toArray();
        if ($edit_data->category_id && !in_array($edit_data->category_id, $select_category_ids)) {
            $select_category_ids[] = $edit_data->category_id;
        }

        $categories = Category::where('status', 1)->select('id', 'name')->get();
        $products = Product::where('status', 1)->select('id', 'name', 'status', 'category_id')->get();

        return view('backEnd.campaign.edit', compact('edit_data', 'products', 'select_products', 'select_product_ids', 'categories', 'select_category_ids'));
    }

    public function update(Request $request)
    { 
        $this->validate($request, [
            'name' => 'required',
            'short_description' => 'nullable',
            'description' => 'nullable',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_one' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_two' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_three' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'category_id' => 'nullable',
            'product_id' => 'nullable|array',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'review' => 'nullable',
            'deadline' => 'nullable',
            'top_title_1' => 'nullable|string|max:255',
            'top_title_2' => 'nullable|string|max:255',
            'heading_1' => 'nullable|string|max:255',
            'feature_1' => 'nullable|string|max:255',
            'feature_2' => 'nullable|string|max:255',
            'heading_2' => 'nullable|string|max:255',
            'heading_3' => 'nullable|string|max:255',
            'heading_4' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'billing_details' => 'nullable|string|max:255',
        ]);

        $update_data = Campaign::findOrFail($request->hidden_id);
        $input = $request->except('hidden_id', 'product_id', 'category_id', 'product_ids', 'files', 'image');
        $input['status'] = $request->has('status') ? 1 : 0;
        $input['video'] = $this->getYouTubeVideoId($request->video);

        $categoryIds = [];
        if ($request->has('category_id')) {
            $categoryIds = is_array($request->category_id) ? array_filter($request->category_id) : [$request->category_id];
        }

        // Resolve product IDs (from explicit product_id array + category products if selected)
        $allProductIds = [];
        if (!empty($categoryIds)) {
            $catProductIds = Product::whereIn('category_id', $categoryIds)->where('status', 1)->pluck('id')->toArray();
            $allProductIds = array_merge($allProductIds, $catProductIds);
        }
        if ($request->has('product_id') && is_array($request->product_id)) {
            $allProductIds = array_merge($allProductIds, $request->product_id);
        }
        $allProductIds = array_unique(array_filter($allProductIds));

        $input['category_id'] = !empty($categoryIds) ? reset($categoryIds) : $update_data->category_id;
        if (!empty($allProductIds)) {
            $input['product_id'] = reset($allProductIds);
        }

        // Handle Banner Image
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time() . '-' . strtolower(preg_replace('/\s+/', '-', $banner->getClientOriginalName()));
            $uploadPath = 'public/uploads/campaign/';
            $bannerUrl = $uploadPath . $bannerName;
            $banner->move($uploadPath, $bannerName);
            $input['banner'] = $bannerUrl;
            if ($update_data->banner) {
                File::delete($update_data->banner);
            }
        }

        // Handle Image One
        if ($request->hasFile('image_one')) {
            $image_one = $request->file('image_one');
            $name1 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_one->getClientOriginalName()));
            $name1 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name1);
            $uploadPath1 = 'public/uploads/campaign/';
            $imageUrl1 = $uploadPath1 . $name1;
            $img1 = Image::make($image_one->getRealPath());
            $img1->encode('webp', 90);
            $img1->save($imageUrl1);
            $input['image_one'] = $imageUrl1;
            if ($update_data->image_one) {
                File::delete($update_data->image_one);
            }
        }

        // Handle Image Two
        if ($request->hasFile('image_two')) {
            $image_two = $request->file('image_two');
            $name2 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_two->getClientOriginalName()));
            $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name2);
            $uploadPath2 = 'public/uploads/campaign/';
            $imageUrl2 = $uploadPath2 . $name2;
            $img2 = Image::make($image_two->getRealPath());
            $img2->encode('webp', 90);
            $img2->save($imageUrl2);
            $input['image_two'] = $imageUrl2;
            if ($update_data->image_two) {
                File::delete($update_data->image_two);
            }
        }

        // Handle Image Three
        if ($request->hasFile('image_three')) {
            $image_three = $request->file('image_three');
            $name3 = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image_three->getClientOriginalName()));
            $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name3);
            $uploadPath3 = 'public/uploads/campaign/';
            $imageUrl3 = $uploadPath3 . $name3;
            $img3 = Image::make($image_three->getRealPath());
            $img3->encode('webp', 90);
            $img3->save($imageUrl3);
            $input['image_three'] = $imageUrl3;
            if ($update_data->image_three) {
                File::delete($update_data->image_three);
            }
        }

        $input['slug'] = strtolower(Str::slug($request->name));
        $update_data->update($input);

        // Sync categories
        if (!empty($categoryIds)) {
            $update_data->categories()->sync($categoryIds);
        } else {
            $update_data->categories()->detach();
        }

        // Sync products
        if (!empty($allProductIds)) {
            $update_data->products()->sync($allProductIds);
        }

        // Handle review images
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $name = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image->getClientOriginalName()));
                $uploadPath = 'public/uploads/campaign/';
                $image->move($uploadPath, $name);
                $imageUrl = $uploadPath . $name;

                $pimage = new CampaignReview();
                $pimage->campaign_id = $update_data->id;
                $pimage->image = $imageUrl;
                $pimage->save();
            }
        }

        Toastr::success('Success', 'Campaign updated successfully');
        return redirect()->route('campaign.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Campaign::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Campaign inactive successfully');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = Campaign::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Campaign active successfully');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = Campaign::find($request->hidden_id);
        if ($delete_data) {
            $delete_data->delete();
        }
        Toastr::success('Success', 'Campaign deleted successfully');
        return redirect()->back();
    }

    public function imgdestroy(Request $request)
    { 
        $delete_data = CampaignReview::find($request->id);
        if ($delete_data) {
            File::delete($delete_data->image);
            $delete_data->delete();
        }
        Toastr::success('Success', 'Image deleted successfully');
        return redirect()->back();
    } 

    public function getYouTubeVideoId($input)
    {
        if (empty($input)) return null;
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
        preg_match($pattern, $input, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}
