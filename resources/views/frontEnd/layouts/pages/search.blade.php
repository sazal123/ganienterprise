@extends('frontEnd.layouts.master') 
@section('title', 'Search: ' . $keyword) 
@push('css')
<style>
/* ───── Page Header & Breadcrumbs (Matching Category/PDP) ───── */
.search-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 36px 0 32px;
    position: relative;
    overflow: hidden;
}
.search-header::before {
    content: ''; position: absolute; top: -50%; right: -20%;
    width: 500px; height: 500px; border-radius: 50%;
    background: rgba(201,168,76,0.06);
}
.search-header::after {
    content: ''; position: absolute; bottom: -30%; left: -10%;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(60,125,23,0.08);
}
.search-header-inner { position: relative; z-index: 1; }
.search-title {
    font-family: 'Playfair Display', serif;
    font-size: 26px; font-weight: 700; color: #ffffff; margin: 0 0 8px;
    text-align: center;
}
.search-breadcrumb {
    display: none;
    color: rgba(255,255,255,0.7); flex-wrap: wrap;
}
.search-breadcrumb a { color: rgba(255,255,255,0.7); text-decoration: none; font-weight: 500; transition: color 0.2s; }
.search-breadcrumb a:hover { color: #C9A84C; }
.search-breadcrumb strong { color: #C9A84C; font-weight: 600; }
.search-breadcrumb span { color: rgba(255,255,255,0.4); }

/* ───── Main Layout ───── */
.cat-layout { padding: 36px 0 60px; background: #f8f8f6; min-height: 600px; }

/* ───── Toolbar ───── */
.cat-toolbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    background: #fff; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
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

/* ───── Senior Designer Empty State ───── */
.search-empty-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 50px 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid #f0f0f0;
    max-width: 680px;
    margin: 10px auto 0;
}
.search-empty-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 86px;
    height: 86px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(60,125,23,0.08), rgba(201,168,76,0.12));
    margin-bottom: 24px;
}
.search-empty-icon-inner {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}
.search-empty-icon-inner i {
    font-size: 26px;
    color: #3c7d17;
}
.search-empty-title {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 10px;
}
.search-empty-desc {
    font-size: 14px;
    color: #666666;
    max-width: 480px;
    margin: 0 auto 24px;
    line-height: 1.6;
}
.search-highlight {
    color: #1a1a2e;
    font-weight: 700;
}
.search-empty-suggestions {
    margin-bottom: 30px;
    padding-top: 20px;
    border-top: 1px dashed #e5e5e5;
}
.suggestions-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}
.suggestions-tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
}
.suggestion-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: #f8f8f6;
    border: 1px solid #e8e8e5;
    border-radius: 20px;
    font-size: 13px;
    color: #444;
    text-decoration: none;
    transition: all 0.2s ease;
}
.suggestion-tag i {
    font-size: 11px;
    color: #C9A84C;
}
.suggestion-tag:hover {
    background: #3c7d17;
    border-color: #3c7d17;
    color: #ffffff;
}
.suggestion-tag:hover i {
    color: #ffffff;
}
.search-empty-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}
.btn-search-explore {
    display: inline-flex;
    align-items: center;
    padding: 12px 28px;
    background: #000;
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    border-radius: 30px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(60,125,23,0.25);
    transition: all 0.25s ease;
}
.btn-search-explore:hover {
    background: #000;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(60,125,23,0.35);
}
.btn-search-home {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    background: #f0f2f5;
    color: #444444;
    font-size: 14px;
    font-weight: 600;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.25s ease;
}
.btn-search-home:hover {
    background: #e2e5e9;
    color: #1a1a2e;
}
</style>
@endpush 

@section('content')
{{-- ───── Luxury Header & Breadcrumbs ───── --}}
<section class="search-header">
    <div class="container">
        <div class="search-header-inner">
            <h1 class="search-title">Search Results</h1>
            <div class="search-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>›</span>
                <a href="{{ route('shop') }}">Shop</a>
                <span>›</span>
                <strong>"{{ $keyword }}"</strong>
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

            {{-- Product Grid / Empty State Area --}}
            <div class="col-lg-9 col-12">
                @if($products->count() > 0)
                    {{-- Toolbar (Only shown when products exist) --}}
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
                    {{-- Senior Designer Empty State Card --}}
                    <div class="search-empty-card">
                        <div class="search-empty-icon-wrap">
                            <div class="search-empty-icon-inner">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                        </div>
                        <h3 class="search-empty-title">No Matching Products Found</h3>
                        <p class="search-empty-desc">
                            We couldn't find any products matching <span class="search-highlight">"{{ $keyword }}"</span>.
                            Try checking for spelling errors or searching with broader keywords.
                        </p>

                        @if(isset($categories) && $categories->count() > 0)
                        <div class="search-empty-suggestions">
                            <span class="suggestions-label">Popular Categories</span>
                            <div class="suggestions-tags">
                                @foreach($categories->take(6) as $cat)
                                <a href="{{ route('category', $cat->slug) }}" class="suggestion-tag">
                                    <i class="fa-solid fa-tag"></i> {{ $cat->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="search-empty-actions">
                            <a href="{{ route('shop') }}" class="btn-search-explore">
                                <i class="fa-solid fa-store me-2"></i> Explore All Products
                            </a>
                            <a href="{{ route('home') }}" class="btn-search-home">
                                <i class="fa-solid fa-house me-2"></i> Back to Home
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection