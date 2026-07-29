<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use Image;
use File;
use Toastr;
use Str;

class OfferController extends Controller
{
    public function index()
    {
        $show_data = Offer::withCount('products')->orderBy('id', 'DESC')->get();
        return view('backEnd.offer.index', compact('show_data'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->select('id', 'name', 'new_price', 'product_code')->get();
        $categories = Category::where('status', 1)->select('id', 'name')->get();
        return view('backEnd.offer.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $bannerUrl = null;
        $image = $request->file('banner');
        if ($image) {
            $ext = strtolower($image->getClientOriginalExtension());
            if (!$ext) { $ext = 'jpg'; }
            $filenameOnly = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = time() . '-' . strtolower(preg_replace('/\s+/', '-', $filenameOnly)) . '.' . $ext;

            $uploadpath = 'uploads/offer/';
            $bannerUrl = $uploadpath . $name;

            $dir1 = public_path('uploads/offer/');
            $dir2 = base_path('uploads/offer/');
            $dir3 = base_path('public/uploads/offer/');
            $dir4 = base_path('../public_html/uploads/offer/');

            foreach ([$dir1, $dir2, $dir3, $dir4] as $dir) {
                if (!File::exists($dir)) {
                    @File::makeDirectory($dir, 0777, true, true);
                }
            }

            try {
                $img = Image::make($image->getRealPath());
                if ($ext == 'webp') {
                    @$img->encode('webp', 90);
                }
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($dir1 . $name);
            } catch (\Exception $e) {
                $image->move($dir1, $name);
            }

            if (!File::exists($dir1 . $name)) {
                $image->move($dir1, $name);
            }

            @copy($dir1 . $name, $dir2 . $name);
            @copy($dir1 . $name, $dir3 . $name);
            @copy($dir1 . $name, $dir4 . $name);
        }

        $input = $request->except(['product_ids', 'banner']);
        $slug = Str::slug($request->title);
        $count = Offer::where('slug', 'LIKE', "{$slug}%")->count();
        $input['slug'] = $count ? "{$slug}-{$count}" : $slug;
        $input['banner'] = $bannerUrl;
        $input['status'] = $request->status ? 1 : 0;

        $offer = Offer::create($input);

        if ($request->has('product_ids')) {
            $offer->products()->sync($request->product_ids);
        }

        Toastr::success('Success', 'Offer created successfully');
        return redirect()->route('offer.index');
    }

    public function edit($id)
    {
        $edit_data = Offer::with('products')->findOrFail($id);
        $products = Product::where('status', 1)->select('id', 'name', 'new_price', 'product_code')->get();
        $select_product_ids = $edit_data->products->pluck('id')->toArray();
        return view('backEnd.offer.edit', compact('edit_data', 'products', 'select_product_ids'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'hidden_id' => 'required',
        ]);

        $update_data = Offer::findOrFail($request->hidden_id);
        $input = $request->except(['product_ids', 'banner', 'hidden_id']);

        $image = $request->file('banner');
        if ($image) {
            $ext = strtolower($image->getClientOriginalExtension());
            if (!$ext) { $ext = 'jpg'; }
            $filenameOnly = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = time() . '-' . strtolower(preg_replace('/\s+/', '-', $filenameOnly)) . '.' . $ext;

            $uploadpath = 'uploads/offer/';
            $bannerUrl = $uploadpath . $name;

            $dir1 = public_path('uploads/offer/');
            $dir2 = base_path('uploads/offer/');
            $dir3 = base_path('public/uploads/offer/');
            $dir4 = base_path('../public_html/uploads/offer/');

            foreach ([$dir1, $dir2, $dir3, $dir4] as $dir) {
                if (!File::exists($dir)) {
                    @File::makeDirectory($dir, 0777, true, true);
                }
            }

            try {
                $img = Image::make($image->getRealPath());
                if ($ext == 'webp') {
                    @$img->encode('webp', 90);
                }
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($dir1 . $name);
            } catch (\Exception $e) {
                $image->move($dir1, $name);
            }

            if (!File::exists($dir1 . $name)) {
                $image->move($dir1, $name);
            }

            @copy($dir1 . $name, $dir2 . $name);
            @copy($dir1 . $name, $dir3 . $name);
            @copy($dir1 . $name, $dir4 . $name);

            $input['banner'] = $bannerUrl;

            if ($update_data->banner) {
                $oldFile = str_replace('public/', '', $update_data->banner);
                @File::delete(public_path($oldFile));
                @File::delete(base_path($oldFile));
                @File::delete(base_path('public/' . $oldFile));
                @File::delete(base_path('../public_html/' . $oldFile));
            }
        } else {
            $input['banner'] = $update_data->banner;
        }

        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        if ($request->has('product_ids')) {
            $update_data->products()->sync($request->product_ids);
        } else {
            $update_data->products()->detach();
        }

        Toastr::success('Success', 'Offer updated successfully');
        return redirect()->route('offer.index');
    }

    public function active(Request $request)
    {
        $active = Offer::find($request->hidden_id);
        if ($active) {
            $active->status = 1;
            $active->save();
            Toastr::success('Success', 'Offer activated successfully');
        }
        return redirect()->back();
    }

    public function inactive(Request $request)
    {
        $inactive = Offer::find($request->hidden_id);
        if ($inactive) {
            $inactive->status = 0;
            $inactive->save();
            Toastr::success('Success', 'Offer inactivated successfully');
        }
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = Offer::find($request->hidden_id);
        if ($delete_data) {
            if ($delete_data->banner) {
                $oldFile = str_replace('public/', '', $delete_data->banner);
                @File::delete(public_path($oldFile));
                @File::delete(base_path($oldFile));
                @File::delete(base_path('public/' . $oldFile));
                @File::delete(base_path('../public_html/' . $oldFile));
            }
            $delete_data->products()->detach();
            $delete_data->delete();
            Toastr::success('Success', 'Offer deleted successfully');
        }
        return redirect()->back();
    }
}
