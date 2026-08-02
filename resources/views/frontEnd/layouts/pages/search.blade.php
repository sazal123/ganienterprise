@extends('frontEnd.layouts.master') 
@section('title', 'Search Results: ' . $keyword) 
@push('css')
<style>
/* ───── Layout ───── */
.cat-layout { padding: 30px 0 60px; background: #f8f8f6; min-height: 600px; }

/* ───── Toolbar ───── */
.cat-toolbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    background: #fff; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-toolbar-left {
    display: flex; align-items: center; gap: 16px;
}
.cat-toolbar-left .showing-data { font-size: 13px; color: #666; }

.cat-sort-wrapper {
    display: flex; align-items: center; gap: 8px;
}
.cat-sort-wrapper label { font-size: 13px; color: #666; white-space: nowrap; }
.cat-sort-wrapper select {
    padding: 7px 32px 7px 12px; border: 1px solid #e0e0e0; border-radius: 6px;
    font-size: 13px; color: #333;
    background: #fafafa url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23999'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none; -webkit-appearance: none; cursor: pointer;
}
.cat-sort-wrapper select:focus { border-color: #3c7d17; outline: none; }

/* ───── Sidebar ───── */
.cat-sidebar { position: sticky; top: 100px; }
.cat-sidebar-card {
    background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden; margin-bottom: 16px;
}
.cat-sidebar-title {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; font-size: 14px; font-weight: 600; color: #1a1a2e;
    border-bottom: 1px solid #f0f0f0;
}
.cat-sidebar-body { padding: 6px 0; }

.cat-list { list-style: none; padding: 0; margin: 0; }
.cat-item { border-bottom: 1px solid #f5f5f5; }
.cat-item:last-child { border-bottom: none; }

.cat-link {
    display: block; padding: 10px 18px; font-size: 13px; color: #444;
    text-decoration: none; transition: all 0.2s;
}
.cat-link:hover { background: #f8f8f6; color: #3c7d17; }
</style>
@endpush 

@section('content')
{{-- ───── Breadcrumb Header ───── --}}
<section class="pdp-header">
    <div class="container">
        <div class="pdp-header-inner">
            <div class="pdp-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>›</span>
                <a href="{{ route('shop') }}">Shop</a>
                <span>›</span>
                <strong>Search: "{{ $keyword }}"</strong>
            </div>
        </div>
    </div>
</section>

{{-- ───── Main Search Content ───── --}}
<section class="cat-layout">
    <div class="container">
        <div class="row">
            {{-- Categories Sidebar --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="cat-sidebar">
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title">
                            <span>Categories</span>
                        </div>
                        <div class="cat-sidebar-body">
                            <ul class="cat-list">
                                @foreach($categories as $cat)
                                <li class="cat-item">
                                    <a href="{{ route('category', $cat->slug) }}" class="cat-link">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Grid Area --}}
            <div class="col-lg-9 col-12">
                {{-- Toolbar --}}
                <div class="cat-toolbar">
                    <div class="cat-toolbar-left">
                        <span class="showing-data">
                            Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} Results for <strong>"{{ $keyword }}"</strong>
                        </span>
                    </div>
                    <div class="cat-sort-wrapper">
                        <label for="sortSelect">Sort By:</label>
                        <form action="{{ route('search') }}" method="GET" id="sortForm">
                            <input type="hidden" name="keyword" value="{{ $keyword }}" />
                            @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}" />
                            @endif
                            <select name="sort" id="sortSelect" class="sort" onchange="this.form.submit()">
                                <option value="1" @if(request('sort')==1) selected @endif>Latest</option>
                                <option value="2" @if(request('sort')==2) selected @endif>Oldest</option>
                                <option value="3" @if(request('sort')==3) selected @endif>Price: High to Low</option>
                                <option value="4" @if(request('sort')==4) selected @endif>Price: Low to High</option>
                                <option value="5" @if(request('sort')==5) selected @endif>Name: A-Z</option>
                                <option value="6" @if(request('sort')==6) selected @endif>Name: Z-A</option>
                            </select>
                        </form>
                    </div>
                </div>

                {{-- Products Grid --}}
                @if($products->count() > 0)
                <div class="row g-3 g-md-4">
                    @foreach($products as $product)
                    <div class="col-6 col-md-4 col-lg-4">
                        @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
                @else
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="fa-solid fa-magnifying-glass mb-3" style="font-size: 48px; color: #ccc;"></i>
                    <h4 class="text-dark fw-bold">No Products Found</h4>
                    <p class="text-muted">We couldn't find any products matching "{{ $keyword }}". Please try searching with another term.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 mt-2">Back to Home</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection