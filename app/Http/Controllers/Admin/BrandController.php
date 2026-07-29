<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Image;
use File;
use Toastr;
class BrandController extends Controller
{
    
    public function index(Request $request)
    {
        $data = Brand::orderBy('id','DESC')->get();
        return view('backEnd.brand.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.brand.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        // image with intervention 
        $image = $request->file('image');
        if($image){
            $ext = strtolower($image->getClientOriginalExtension());
            if(!$ext) { $ext = 'jpg'; }
            $filenameOnly = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = time().'-'.strtolower(preg_replace('/\s+/', '-', $filenameOnly)).'.'.$ext;

            $uploadpath = 'uploads/brand/';
            $imageUrl = $uploadpath.$name;

            $dir1 = public_path('uploads/brand/');
            $dir2 = base_path('uploads/brand/');
            $dir3 = base_path('public/uploads/brand/');

            foreach ([$dir1, $dir2, $dir3] as $dir) {
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0777, true, true);
                }
            }

            try {
                $img = Image::make($image->getRealPath());
                if($ext == 'webp') {
                    @$img->encode('webp', 90);
                }
                $width = 210;
                $height = 210;
                $img->height() > $img->width() ? $width=null : $height=null;
                $img->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($dir1.$name);
            } catch (\Exception $e) {
                $image->move($dir1, $name);
            }

            if (!File::exists($dir1.$name)) {
                $image->move($dir1, $name);
            }

            @copy($dir1.$name, $dir2.$name);
            @copy($dir1.$name, $dir3.$name);
            @copy($dir1.$name, public_path('../uploads/brand/'.$name));

            // Copy directly to cPanel public_html/uploads/brand/
            if (is_dir(base_path('../public_html'))) {
                $cpanelDir = base_path('../public_html/uploads/brand/');
                if (!File::exists($cpanelDir)) {
                    @File::makeDirectory($cpanelDir, 0777, true, true);
                }
                @copy($dir1.$name, $cpanelDir.$name);
            }
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
                $docRootBrand = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/brand/';
                if (!File::exists($docRootBrand)) {
                    @File::makeDirectory($docRootBrand, 0777, true, true);
                }
                @copy($dir1.$name, $docRootBrand.$name);
            }
        }else{
            $imageUrl = NULL;
        }
       

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/u', '-', trim($request->name)));
        $input['image'] = $imageUrl;
        Brand::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('brands.index');
    }
    
    public function edit($id)
    {
        $edit_data = Brand::find($id);
        return view('backEnd.brand.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Brand::find($request->id);
        $input = $request->all();
        $image = $request->file('image');
        if($image){
            // image with intervention 
            $ext = strtolower($image->getClientOriginalExtension());
            if(!$ext) { $ext = 'jpg'; }
            $filenameOnly = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = time().'-'.strtolower(preg_replace('/\s+/', '-', $filenameOnly)).'.'.$ext;

            $uploadpath = 'uploads/brand/';
            $imageUrl = $uploadpath.$name;

            $dir1 = public_path('uploads/brand/');
            $dir2 = base_path('uploads/brand/');
            $dir3 = base_path('public/uploads/brand/');

            foreach ([$dir1, $dir2, $dir3] as $dir) {
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0777, true, true);
                }
            }

            try {
                $img = Image::make($image->getRealPath());
                if($ext == 'webp') {
                    @$img->encode('webp', 90);
                }
                $width = 210;
                $height = 210;
                $img->height() > $img->width() ? $width=null : $height=null;
                $img->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($dir1.$name);
            } catch (\Exception $e) {
                $image->move($dir1, $name);
            }

            if (!File::exists($dir1.$name)) {
                $image->move($dir1, $name);
            }

            @copy($dir1.$name, $dir2.$name);
            @copy($dir1.$name, $dir3.$name);
            @copy($dir1.$name, public_path('../uploads/brand/'.$name));

            // Copy directly to cPanel public_html/uploads/brand/
            if (is_dir(base_path('../public_html'))) {
                $cpanelDir = base_path('../public_html/uploads/brand/');
                if (!File::exists($cpanelDir)) {
                    @File::makeDirectory($cpanelDir, 0777, true, true);
                }
                @copy($dir1.$name, $cpanelDir.$name);
            }
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
                $docRootBrand = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/brand/';
                if (!File::exists($docRootBrand)) {
                    @File::makeDirectory($docRootBrand, 0777, true, true);
                }
                @copy($dir1.$name, $docRootBrand.$name);
            }

            $input['image'] = $imageUrl;
            if($update_data->image){
                $oldFile = str_replace('public/', '', $update_data->image);
                @File::delete(public_path($oldFile));
                @File::delete(base_path($oldFile));
                @File::delete(base_path('public/'.$oldFile));
                @File::delete(public_path('../'.$oldFile));
            }
        }else{
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('brands.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Brand::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Brand::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Brand::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
