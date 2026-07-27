@extends('backEnd.layouts.master')
@section('title','Notice Create')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('notices.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Notice Create</h4>
            </div>
        </div>
    </div>
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{route('notices.store')}}" method="POST" class=row>
                    @csrf
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="text" class="form-label">Notice Text *</label>
                            <input type="text" class="form-control @error('text') is-invalid @enderror" name="text" value="{{ old('text') }}" id="text" required="">
                            @error('text')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="link" class="form-label">Link (optional)</label>
                            <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') }}" id="link" placeholder="https://...">
                            @error('link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="order_id" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="order_id" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                              <input type="checkbox" value="1" name="status" checked>
                              <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>
@endsection
