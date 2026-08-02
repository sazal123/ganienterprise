@extends('backEnd.layouts.master')
@section('title', 'Customer Queries & Messages')

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Customer Queries & Messages</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Search & Filter -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body py-2">
                    <form action="{{ route('contact_messages.index') }}" method="GET" class="row align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Search by name, phone, email, subject, message...">
                                <button class="btn btn-info" type="submit"><i class="fe-search"></i> Search</button>
                                @if(request('keyword') || request('status'))
                                <a href="{{ route('contact_messages.index') }}" class="btn btn-secondary">Clear</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Replied" {{ request('status') == 'Replied' ? 'selected' : '' }}>Replied</option>
                                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $value->created_at->format('d M Y, h:i A') }}</td>
                                    <td><strong>{{ $value->name }}</strong></td>
                                    <td><a href="tel:{{ $value->phone }}">{{ $value->phone }}</a></td>
                                    <td><a href="mailto:{{ $value->email }}">{{ $value->email }}</a></td>
                                    <td>{{ Str::limit($value->subject, 30) }}</td>
                                    <td>
                                        @if($value->status == 'Pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($value->status == 'In Progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @elseif($value->status == 'Replied')
                                            <span class="badge bg-success">Replied</span>
                                        @else
                                            <span class="badge bg-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button-list">
                                            <a href="{{ route('contact_messages.edit', $value->id) }}" class="btn btn-xs btn-blue waves-effect waves-light" title="View & Edit"><i class="fe-edit"></i> View / Edit</a>
                                            <form method="POST" action="{{ route('contact_messages.destroy') }}" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this query?');">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $value->id }}">
                                                <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light" title="Delete"><i class="fe-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No customer queries found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
