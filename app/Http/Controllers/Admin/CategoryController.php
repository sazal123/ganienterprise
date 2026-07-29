<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Toastr;
use Image;
use File;
use Str;
class CategoryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','store']]);
         $this->middleware('permission:category-create', ['only' => ['create','store']]);
         $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Category::orderBy('id','DESC')->with('category')->get();
        // return $data;
        return view('backEnd.category.index',compact('data'));
    }
    public function create()
    {
        $categories = Category::orderBy('id','DESC')->select('id','name')->get();
        return view('backEnd.category.create',compact('categories'));
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
            
            $uploadpath = 'uploads/category/';
            $imageUrl = $uploadpath.$name;

            $dir1 = public_path('uploads/category/');
            $dir2 = base_path('uploads/category/');
            $dir3 = base_path('public/uploads/category/');

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
                $width = "";
                $height = "";
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
            @copy($dir1.$name, public_path('../uploads/category/'.$name));

            // Copy directly to cPanel public_html/uploads/category/
            if (is_dir(base_path('../public_html'))) {
                $cpanelDir = base_path('../public_html/uploads/category/');
                if (!File::exists($cpanelDir)) {
                    @File::makeDirectory($cpanelDir, 0777, true, true);
                }
                @copy($dir1.$name, $cpanelDir.$name);
            }
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
                $docRootCategory = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/category/';
                if (!File::exists($docRootCategory)) {
                    @File::makeDirectory($docRootCategory, 0777, true, true);
                }
                @copy($dir1.$name, $docRootCategory.$name);
            }
        }else{
            $imageUrl = null;
        }

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);

        $input['parent_id'] = $request->parent_id?$request->parent_id:0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['image'] = $imageUrl;
        Category::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('categories.index');
    }
    
    public function edit($id)
    {
        $edit_data = Category::find($id);
        $categories = Category::select('id','name')->get();
        return view('backEnd.category.edit',compact('edit_data','categories'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Category::find($request->id);
        $input = $request->all();
        $image = $request->file('image');
        if($image){
            // image with intervention 
            $ext = strtolower($image->getClientOriginalExtension());
            if(!$ext) { $ext = 'jpg'; }
            $filenameOnly = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = time().'-'.strtolower(preg_replace('/\s+/', '-', $filenameOnly)).'.'.$ext;

            $uploadpath = 'uploads/category/';
            $imageUrl = $uploadpath.$name;

            $dir1 = public_path('uploads/category/');
            $dir2 = base_path('uploads/category/');
            $dir3 = base_path('public/uploads/category/');

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
                $width = "";
                $height = "";
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
            @copy($dir1.$name, public_path('../uploads/category/'.$name));

            // Copy directly to cPanel public_html/uploads/category/
            if (is_dir(base_path('../public_html'))) {
                $cpanelDir = base_path('../public_html/uploads/category/');
                if (!File::exists($cpanelDir)) {
                    @File::makeDirectory($cpanelDir, 0777, true, true);
                }
                @copy($dir1.$name, $cpanelDir.$name);
            }
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
                $docRootCategory = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/category/';
                if (!File::exists($docRootCategory)) {
                    @File::makeDirectory($docRootCategory, 0777, true, true);
                }
                @copy($dir1.$name, $docRootCategory.$name);
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
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);

        $input['parent_id'] = $request->parent_id?$request->parent_id:0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['status'] = $request->status?1:0;
        
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('categories.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Category::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Category::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Category::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
