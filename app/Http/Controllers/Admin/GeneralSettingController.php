<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Toastr;
use Image;
use File;
use DB;
class GeneralSettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting-list|setting-create|setting-edit|setting-delete', ['only' => ['index','store']]);
        $this->middleware('permission:setting-create', ['only' => ['create','store']]);
        $this->middleware('permission:setting-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $show_data = GeneralSetting::orderBy('id','DESC')->get();
        return view('backEnd.settings.index',compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.settings.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
			'facebook_verification' => 'required',
			'google_verification' => 'required',
			'meta_keyword' => 'required',
			'meta_description' => 'required',
            'white_logo' => 'required',
			'og_baner' => 'required',
            'favicon' => 'required',
            'status' => 'required',
        ]);

        $dir1 = public_path('uploads/settings/');
        $dir2 = base_path('uploads/settings/');
        $dir3 = base_path('public/uploads/settings/');
        $dir4 = base_path('../public_html/uploads/settings/');

        foreach ([$dir1, $dir2, $dir3, $dir4] as $dir) {
            if (!File::exists($dir)) {
                @File::makeDirectory($dir, 0777, true, true);
            }
        }

        // white logo
        $image = $request->file('white_logo');
        $name = strtolower(preg_replace('/\s+/', '-', time().'-white_logo.webp'));
        $imageUrl = 'public/uploads/settings/'.$name;
        $img = Image::make($image->getRealPath());
        try { $img->encode('webp', 90); } catch (\Exception $e) {}
        $img->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($dir1.$name);
        @copy($dir1.$name, $dir2.$name);
        @copy($dir1.$name, $dir3.$name);
        @copy($dir1.$name, $dir4.$name);

        // dark logo
        $image2 = $request->file('dark_logo');
        $name2 = strtolower(preg_replace('/\s+/', '-', time().'-dark_logo.webp'));
        $image2Url = 'public/uploads/settings/'.$name2;
        $img2 = Image::make($image2->getRealPath());
        try { $img2->encode('webp', 90); } catch (\Exception $e) {}
        $img2->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img2->save($dir1.$name2);
        @copy($dir1.$name2, $dir2.$name2);
        @copy($dir1.$name2, $dir3.$name2);
        @copy($dir1.$name2, $dir4.$name2);

        // OG Banner
        $image4 = $request->file('og_baner');
        $name4 = strtolower(preg_replace('/\s+/', '-', time().'-og_banner.webp'));
        $image4Url = 'public/uploads/settings/'.$name4;
        $img4 = Image::make($image4->getRealPath());
        try { $img4->encode('webp', 90); } catch (\Exception $e) {}
        $img4->resize(1440, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img4->save($dir1.$name4);
        @copy($dir1.$name4, $dir2.$name4);
        @copy($dir1.$name4, $dir3.$name4);
        @copy($dir1.$name4, $dir4.$name4);

        // favicon
        $image3 = $request->file('favicon');
        $name3 =  time().'-'.$image3->getClientOriginalName();
        $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.png',$name3);
        $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
        $image3Url = 'public/uploads/settings/'.$name3;
        $img3=Image::make($image3->getRealPath());
        $img3->save($dir1.$name3);
        @copy($dir1.$name3, $dir2.$name3);
        @copy($dir1.$name3, $dir3.$name3);
        @copy($dir1.$name3, $dir4.$name3);

        $input = $request->all();
        $input['white_logo'] = $imageUrl;
        $input['dark_logo'] = $image2Url;
        $input['favicon'] = $image3Url;
        $input['og_baner'] = $image4Url;
        GeneralSetting::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('settings.index');
    }

    public function edit($id)
    {
        $edit_data = GeneralSetting::find($id);
        return view('backEnd.settings.edit',compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $update_data = GeneralSetting::find($request->id);
        $input = $request->all();

        $dir1 = public_path('uploads/settings/');
        $dir2 = base_path('uploads/settings/');
        $dir3 = base_path('public/uploads/settings/');
        $dir4 = base_path('../public_html/uploads/settings/');

        foreach ([$dir1, $dir2, $dir3, $dir4] as $dir) {
            if (!File::exists($dir)) {
                @File::makeDirectory($dir, 0777, true, true);
            }
        }

        // new white logo
        $image = $request->file('white_logo');
        if($image){
            $name = strtolower(preg_replace('/\s+/', '-', time().'-white_logo.webp'));
            $imageUrl = 'public/uploads/settings/'.$name;
            $img = Image::make($image->getRealPath());
            try { $img->encode('webp', 90); } catch (\Exception $e) {}
            $img->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($dir1.$name);
            @copy($dir1.$name, $dir2.$name);
            @copy($dir1.$name, $dir3.$name);
            @copy($dir1.$name, $dir4.$name);
            $input['white_logo'] = $imageUrl;
        }else{
            $input['white_logo'] = $update_data->white_logo;
        }
        // new dark logo
        $image2 = $request->file('dark_logo');
        if($image2){
            $name2 = strtolower(preg_replace('/\s+/', '-', time().'-dark_logo.webp'));
            $image2Url = 'public/uploads/settings/'.$name2;
            $img2 = Image::make($image2->getRealPath());
            try { $img2->encode('webp', 90); } catch (\Exception $e) {}
            $img2->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img2->save($dir1.$name2);
            @copy($dir1.$name2, $dir2.$name2);
            @copy($dir1.$name2, $dir3.$name2);
            @copy($dir1.$name2, $dir4.$name2);
            $input['dark_logo'] = $image2Url;
        }else{
            $input['dark_logo'] = $update_data->dark_logo;
        }

        // new OG image
        $image4 = $request->file('og_baner');
        if($image4){
            $name4 = strtolower(preg_replace('/\s+/', '-', time().'-og_banner.webp'));
            $image4Url = 'public/uploads/settings/'.$name4;
            $img4 = Image::make($image4->getRealPath());
            try { $img4->encode('webp', 90); } catch (\Exception $e) {}
            $img4->resize(1440, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img4->save($dir1.$name4);
            @copy($dir1.$name4, $dir2.$name4);
            @copy($dir1.$name4, $dir3.$name4);
            @copy($dir1.$name4, $dir4.$name4);
            $input['og_baner'] = $image4Url;
        }else{
            $input['og_baner'] = $update_data->og_baner;
        }

        // new favicon image
        $image3 = $request->file('favicon');
        if($image3){
            $name3 = strtolower(preg_replace('/\s+/', '-', time().'-favicon.webp'));
            $image3Url = 'public/uploads/settings/'.$name3;
            $img3 = Image::make($image3->getRealPath());
            try { $img3->encode('webp', 90); } catch (\Exception $e) {}
            $img3->resize(32, 32);
            $img3->save($dir1.$name3);
            @copy($dir1.$name3, $dir2.$name3);
            @copy($dir1.$name3, $dir3.$name3);
            @copy($dir1.$name3, $dir4.$name3);
            $input['favicon'] = $image3Url;
        }else{
            $input['favicon'] = $update_data->favicon;
        }
        $input['status'] = 1;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('settings.index');
    }

    public function inactive(Request $request)
    {
        $inactive = GeneralSetting::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = GeneralSetting::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = GeneralSetting::find($request->hidden_id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
