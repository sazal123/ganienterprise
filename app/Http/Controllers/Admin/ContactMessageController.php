<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Brian2694\Toastr\Facades\Toastr;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->keyword) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%")
                  ->orWhere('subject', 'LIKE', "%{$keyword}%")
                  ->orWhere('message', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('id', 'DESC')->paginate(20);

        return view('backEnd.contact_messages.index', compact('data'));
    }

    public function edit($id)
    {
        $data = ContactMessage::findOrFail($id);
        return view('backEnd.contact_messages.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_messages,id',
            'status' => 'required|string',
        ]);

        $contactMessage = ContactMessage::findOrFail($request->id);
        $contactMessage->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        Toastr::success('Success', 'Customer query updated successfully');
        return redirect()->route('contact_messages.index');
    }

    public function destroy(Request $request)
    {
        $contactMessage = ContactMessage::findOrFail($request->id);
        $contactMessage->delete();

        Toastr::success('Success', 'Customer query deleted successfully');
        return redirect()->back();
    }
}
