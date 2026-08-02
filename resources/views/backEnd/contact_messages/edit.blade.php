@extends('backEnd.layouts.master')
@section('title', 'Manage Customer Query #' . $data->id)

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('contact_messages.index') }}" class="btn btn-primary rounded-pill"><i class="fe-arrow-left"></i> Back to List</a>
                </div>
                <h4 class="page-title">Manage Customer Query #{{ $data->id }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <!-- Inquiry Details Card -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fe-mail me-1"></i> Inquiry Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <th style="width: 140px;">Submitted On:</th>
                                <td>{{ $data->created_at->format('d M Y, h:i A') }} ({{ $data->created_at->diffForHumans() }})</td>
                            </tr>
                            <tr>
                                <th>Customer Name:</th>
                                <td><strong>{{ $data->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Phone Number:</th>
                                <td><a href="tel:{{ $data->phone }}"><i class="fe-phone me-1"></i> {{ $data->phone }}</a></td>
                            </tr>
                            <tr>
                                <th>Email Address:</th>
                                <td><a href="mailto:{{ $data->email }}"><i class="fe-mail me-1"></i> {{ $data->email }}</a></td>
                            </tr>
                            <tr>
                                <th>Subject:</th>
                                <td><span class="fw-semibold text-dark">{{ $data->subject }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <div>
                        <h6 class="fw-bold mb-2">Customer Message:</h6>
                        <div class="p-3 bg-light rounded border text-secondary" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ $data->message }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Notes Management Card -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fe-settings me-1"></i> Query Management</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contact_messages.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Query Status *</label>
                            <select name="status" class="form-control form-select @error('status') is-invalid @enderror" required>
                                <option value="Pending" {{ $data->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $data->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Replied" {{ $data->status == 'Replied' ? 'selected' : '' }}>Replied</option>
                                <option value="Closed" {{ $data->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Admin Internal Notes / Reply Summary</label>
                            <textarea name="admin_notes" rows="4" class="form-control" placeholder="Enter resolution notes, response details, or internal remarks...">{{ old('admin_notes', $data->admin_notes) }}</textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fe-check-circle me-1"></i> Save Changes</button>
                    </form>

                    <form method="POST" action="{{ route('contact_messages.destroy') }}" onsubmit="return confirm('Are you sure you want to delete this query permanently?');">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <button type="submit" class="btn btn-outline-danger waves-effect"><i class="fe-trash-2 me-1"></i> Delete Query</button>
                    </form>
                        </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
