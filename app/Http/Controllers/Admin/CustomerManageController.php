<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\CustomerProfit;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\IpBlock;
use App\Models\District;
use Toastr;
use Image;
use File;
use Auth;
use Hash;
use DB;

class CustomerManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Advanced search across all fields
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%")
                  ->orWhere('address', 'LIKE', "%{$keyword}%")
                  ->orWhere('customer_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('whatsapp', 'LIKE', "%{$keyword}%")
                  ->orWhere('feedback', 'LIKE', "%{$keyword}%")
                  ->orWhere('status', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $show_data = $query->with('orders', 'cust_area')->withCount('orders')->orderBy('id', 'DESC')->paginate(50);
        $districts = District::orderBy('district', 'ASC')->get();

        // Generate month headers: Apr/26 through Dec/26
        $months = [];
        $currentYear = date('y');
        $monthsList = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($monthsList as $m) {
            $months[] = $m . '/' . $currentYear;
        }

        // Pre-compute monthly order totals for all customers on this page
        $customerIds = $show_data->pluck('id');
        $monthlyOrders = \App\Models\Order::selectRaw('customer_id, DATE_FORMAT(created_at, "%b/%y") as month_key, SUM(amount) as total')
            ->whereIn('customer_id', $customerIds)
            ->whereYear('created_at', '=', '20' . $currentYear)
            ->whereIn(DB::raw('MONTH(created_at)'), [4, 5, 6, 7, 8, 9, 10, 11, 12])
            ->groupBy('customer_id', 'month_key')
            ->get()
            ->groupBy('customer_id');

        return view('backEnd.customer.index', compact('show_data', 'districts', 'months', 'monthlyOrders'));
    }

    public function customerReport(Request $request)
    {
        $query = Customer::query();

        // Search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%")
                  ->orWhere('address', 'LIKE', "%{$keyword}%")
                  ->orWhere('customer_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('status', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter by status dropdown (overrides keyword status match)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->start_date) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            });
        }
        if ($request->end_date) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            });
        }

        $show_data = $query->with('orders', 'cust_area')->withCount('orders')->orderBy('id', 'DESC')->get();

        // Generate month headers: Apr/26 through Dec/26
        $months = [];
        $currentYear = date('y');
        $monthsList = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($monthsList as $m) {
            $months[] = $m . '/' . $currentYear;
        }

        // Pre-compute monthly order totals for all customers
        $customerIds = $show_data->pluck('id');
        $monthlyOrders = \App\Models\Order::selectRaw('customer_id, DATE_FORMAT(created_at, "%b/%y") as month_key, SUM(amount) as total')
            ->whereIn('customer_id', $customerIds)
            ->whereYear('created_at', '=', '20' . $currentYear)
            ->whereIn(DB::raw('MONTH(created_at)'), [4, 5, 6, 7, 8, 9, 10, 11, 12])
            ->groupBy('customer_id', 'month_key')
            ->get()
            ->groupBy('customer_id');

        // Compute summary totals
        $totalCustomers = $show_data->count();
        $totalDeals = $show_data->sum(function ($c) { return $c->orders->count(); });
        $totalRevenue = $show_data->sum(function ($c) { return $c->orders->sum('amount'); });

        $districts = District::orderBy('district', 'ASC')->get();

        return view('backEnd.customer.report', compact('show_data', 'districts', 'months', 'monthlyOrders', 'totalCustomers', 'totalDeals', 'totalRevenue'));
    }

    public function create()
    {
        $districts = District::orderBy('district', 'ASC')->get();
        return view('backEnd.customer.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:customers,email',
        ]);

        $input = $request->except('password');
        $input['password'] = Hash::make($request->password ?: '123456');
        $input['status'] = $request->status ?? 'General';
        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name));

        // Handle image upload
        $image = $request->file('image');
        if ($image) {
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('/\.(jpg|jpeg|png|webp)$/', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 100;
            $height = 100;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
        }

        $customer = Customer::create($input);

        // Generate customer code: GE-OR{year}-{id padded} (e.g., GE-OR26-01)
        $customer->customer_code = 'GE-OR' . date('y') . '-' . str_pad($customer->id, 2, '0', STR_PAD_LEFT);
        $customer->save();

        Toastr::success('Success', 'Customer created successfully');
        return redirect()->route('customers.index');
    }

    public function edit($id)
    {
        $edit_data = Customer::find($id);
        $districts = District::orderBy('district', 'ASC')->get();
        return view('backEnd.customer.edit', compact('edit_data', 'districts'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $input = $request->except('hidden_id');
        $update_data = Customer::find($request->hidden_id);

        // Handle password
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        // Handle image
        $image = $request->file('image');
        if ($image) {
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('/\.(jpg|jpeg|png|webp)$/', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 100;
            $height = 100;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status ?? 'General';
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('customers.index');
    }

    public function destroy(Request $request)
    {
        $delete_data = Customer::find($request->hidden_id);
        if ($delete_data) {
            File::delete($delete_data->image);
            $delete_data->delete();
            Toastr::success('Success', 'Customer deleted successfully');
        }
        return redirect()->back();
    }

    public function inactive(Request $request)
    {
        $inactive = Customer::find($request->hidden_id);
        $inactive->status = 'Inactive';
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = Customer::find($request->hidden_id);
        $active->status = 'General';
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }

    public function profile(Request $request)
    {
        $profile = Customer::with('orders')->find($request->id);
        return view('backEnd.customer.profile', compact('profile'));
    }

    public function adminlog(Request $request)
    {
        $customer = Customer::find($request->hidden_id);
        Auth::guard('customer')->loginUsingId($customer->id);
        return redirect()->route('customer.account');
    }

    public function ip_block(Request $request)
    {
        $data = IpBlock::get();
        return view('backEnd.reports.ipblock', compact('data'));
    }

    public function ipblock_store(Request $request)
    {
        $store_data = new IpBlock();
        $store_data->ip_no = $request->ip_no;
        $store_data->reason = $request->reason;
        $store_data->save();
        Toastr::success('Success', 'IP address add successfully');
        return redirect()->back();
    }

    public function ipblock_update(Request $request)
    {
        $update_data = IpBlock::find($request->id);
        $update_data->ip_no = $request->ip_no;
        $update_data->reason = $request->reason;
        $update_data->save();
        Toastr::success('Success', 'IP address update successfully');
        return redirect()->back();
    }

    public function ipblock_destroy(Request $request)
    {
        $delete_data = IpBlock::find($request->id)->delete();
        Toastr::success('Success', 'IP address delete successfully');
        return redirect()->back();
    }
}
