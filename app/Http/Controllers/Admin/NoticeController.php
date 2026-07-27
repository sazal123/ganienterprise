<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use Toastr;

class NoticeController extends Controller
{
    public function index()
    {
        $data = Notice::orderBy('order_id', 'ASC')->orderBy('id', 'DESC')->get();
        return view('backEnd.notice.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.notice.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
        ]);

        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['order_id'] = $request->order_id ?? 0;
        Notice::create($input);

        Toastr::success('Success', 'Notice created successfully');
        return redirect()->route('notices.index');
    }

    public function edit($id)
    {
        $edit_data = Notice::findOrFail($id);
        return view('backEnd.notice.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['text' => 'required']);
        $update_data = Notice::findOrFail($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['order_id'] = $request->order_id ?? 0;
        $update_data->update($input);

        Toastr::success('Success', 'Notice updated successfully');
        return redirect()->route('notices.index');
    }

    public function inactive(Request $request)
    {
        $notice = Notice::findOrFail($request->hidden_id);
        $notice->status = 0;
        $notice->save();
        Toastr::success('Success', 'Notice inactive successfully');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $notice = Notice::findOrFail($request->hidden_id);
        $notice->status = 1;
        $notice->save();
        Toastr::success('Success', 'Notice active successfully');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $notice = Notice::findOrFail($request->hidden_id);
        $notice->delete();
        Toastr::success('Success', 'Notice deleted successfully');
        return redirect()->back();
    }
}
