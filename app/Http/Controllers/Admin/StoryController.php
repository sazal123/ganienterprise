<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Product;
use Toastr;

class StoryController extends Controller
{
    public function index()
    {
        $data = Story::with('product')->orderBy('order_id', 'ASC')->orderBy('id', 'DESC')->get();
        return view('backEnd.story.index', compact('data'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->select('id', 'name')->get();
        return view('backEnd.story.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['video' => 'required']);

        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['order_id'] = $request->order_id ?? 0;

        // Handle video upload
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/stories/');
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $file->move($uploadPath, $name);
            $input['video'] = 'uploads/stories/' . $name;
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time() . '-thumb.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/stories/');
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $file->move($uploadPath, $name);
            $input['thumbnail'] = 'uploads/stories/' . $name;
        }

        Story::create($input);
        Toastr::success('Success', 'Story created successfully');
        return redirect()->route('stories.index');
    }

    public function edit($id)
    {
        $edit_data = Story::findOrFail($id);
        $products = Product::where('status', 1)->select('id', 'name')->get();
        return view('backEnd.story.edit', compact('edit_data', 'products'));
    }

    public function update(Request $request)
    {
        $update_data = Story::findOrFail($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['order_id'] = $request->order_id ?? 0;

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/stories/');
            $file->move($uploadPath, $name);
            $input['video'] = 'uploads/stories/' . $name;
            // Delete old
            $oldVideo = $update_data->video;
            if ($oldVideo && file_exists(public_path($oldVideo))) @unlink(public_path($oldVideo));
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time() . '-thumb.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/stories/');
            $file->move($uploadPath, $name);
            $input['thumbnail'] = 'uploads/stories/' . $name;
            $oldThumb = $update_data->thumbnail;
            if ($oldThumb && file_exists(public_path($oldThumb))) @unlink(public_path($oldThumb));
        }

        $update_data->update($input);
        Toastr::success('Success', 'Story updated successfully');
        return redirect()->route('stories.index');
    }

    public function destroy(Request $request)
    {
        $story = Story::findOrFail($request->hidden_id);
        if ($story->video && file_exists(public_path($story->video))) @unlink(public_path($story->video));
        if ($story->thumbnail && file_exists(public_path($story->thumbnail))) @unlink(public_path($story->thumbnail));
        $story->delete();
        Toastr::success('Success', 'Story deleted successfully');
        return redirect()->back();
    }
}
