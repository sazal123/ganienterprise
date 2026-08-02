@extends('frontEnd.layouts.master')
@section('title', $subcategory->meta_title ?? $subcategory->subcategoryName)
@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/jquery-ui.css') }}" />
<style>
/* ───── Subcategory Page Styles (Matching Category Page) ───── */
.cat-layout { padding: 30px 0 60px; background: #f8f8f6; min-height: 600px; }
.pdp-header{display:none;}

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
.mobile-filter-btn {
    display: none; align-items: center; gap: 6px;
    padding: 7px 16px; background: #3c7d17; color: #fff; border: none;
    border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;
}
.mobile-filter-btn:hover { background: #2d5d11; }

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
.cat-sidebar-card:last-child { margin-bottom: 0; }

.cat-sidebar-title {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; font-size: 14px; font-weight: 600; color: #1a1a2e;
    border-bottom: 1px solid #f0f0f0; cursor: pointer; user-select: none;
}
.cat-sidebar-title:hover { background: #fafaf8; }
.cat-sidebar-title i { font-size: 10px; color: #999; transition: transform 0.3s; }
.cat-sidebar-title.active i { transform: rotate(180deg); }
.cat-sidebar-body { padding: 6px 0; }
.cat-sidebar-body.hidden { display: none; }

/* Category Tree */
.cat-list { list-style: none; padding: 0; margin: 0; }
.cat-item { border-bottom: 1px solid #f5f5f5; }
.cat-item:last-child { border-bottom: none; }

.cat-link {
    display: block; padding: 10px 18px; font-size: 13px; color: #444;
    text-decoration: none; transition: all 0.2s;
}
.cat-link:hover { background: #f8f8f6; color: #3c7d17; }
.cat-link.active { color: #3c7d17; font-weight: 600; background: rgba(60,125,23,0.04); }

.cat-toggle {
    background: none; border: none; padding: 10px 18px; width: 100%; text-align: left;
    font-size: 13px; color: #444; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between;
}
.cat-toggle:hover { background: #f8f8f6; color: #3c7d17; }
.cat-toggle i { font-size: 10px; color: #bbb; transition: transform 0.3s; }
.cat-toggle.active i { transform: rotate(90deg); }

.cat-sub-list {
    list-style: none; padding: 0 0 4px 20px; margin: 0; display: none;
}
.cat-sub-list.open { display: block; }
.cat-sub-link {
    display: block; padding: 8px 18px 8px 16px; font-size: 12px; color: #666;
    text-decoration: none; border-left: 2px solid transparent; transition: all 0.2s;
}
.cat-sub-link:hover { color: #3c7d17; border-left-color: #3c7d17; background: rgba(60,125,23,0.03); }
.cat-sub-link.active { color: #3c7d17; font-weight: 600; border-left-color: #3c7d17; background: rgba(60,125,23,0.04); }

/* Price Range */
.cat-price-range { padding: 18px; }
.price-inputs {
    display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 18px;
}
.price-input-group {
    flex: 1; display: flex; align-items: center;
    background: #f5f5f5; border: 1px solid #e8e8e8; border-radius: 6px;
    padding: 6px 10px; font-size: 13px; color: #666;
}
.price-input-group span { margin-right: 4px; }
.price-input-group input {
    width: 100%; border: none; background: transparent;
    font-size: 13px; font-weight: 600; color: #333; outline: none;
}
.price-separator { color: #ccc; font-size: 13px; }

#cat-price-range {
    margin: 0 4px; height: 4px; border: none;
    background: #e8e8e8; border-radius: 2px;
}
#cat-price-range .ui-slider-range {
    background: linear-gradient(90deg, #3c7d17, #5a9e2a); border-radius: 2px;
}
#cat-price-range .ui-slider-handle {
    width: 18px; height: 18px; border: 2px solid #3c7d17; background: #fff;
    border-radius: 50%; top: -7px; cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}
#cat-price-range .ui-slider-handle:hover { transform: scale(1.15); box-shadow: 0 2px 8px rgba(60,125,23,0.3); }

.price-filter-actions { display: flex; gap: 8px; margin-top: 16px; }
.btn-price-filter {
    flex: 1; padding: 9px 0; background: #C9A84C; color: #fff;
    border: none; border-radius: 6px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-align: center;
}
.btn-price-filter:hover { background: #2d5d11; }
.btn-price-reset {
    padding: 9px 16px; background: #f0f0f0; color: #666;
    border: none; border-radius: 6px; font-size: 13px; cursor: pointer;
}
.btn-price-reset:hover { background: #e0e0e0; color: #333; }

/* Filter Checkboxes */
.cat-filter-list { list-style: none; padding: 4px 0; margin: 0; }
.cat-filter-label {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 18px; cursor: pointer; transition: background 0.2s;
}
.cat-filter-label:hover { background: #f8f8f6; }
.cat-filter-label input[type="checkbox"] {
    appearance: none; -webkit-appearance: none;
    width: 17px; height: 17px; border: 2px solid #d0d0d0;
    border-radius: 4px; cursor: pointer; position: relative; flex-shrink: 0;
}
.cat-filter-label input[type="checkbox"]:checked {
    background: #3c7d17; border-color: #3c7d17;
}
.cat-filter-label input[type="checkbox"]:checked::after {
    content: '✓'; position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%); color: #fff; font-size: 11px; font-weight: 700;
}
.cat-filter-label .filter-name { font-size: 13px; color: #444; }

/* ───── Product Grid ───── */
.cat-products-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
@media (max-width: 1199px) { .cat-products-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px) { .cat-products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }

/* ───── Pagination ───── */
.cat-pagination-wrap {
    margin-top: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: #fff;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-pagination-info { font-size: 13px; color: #888; }
.cat-pagination-info strong { color: #333; font-weight: 600; }
.cat-pagination { display: flex; align-items: center; gap: 4px; }
.cat-pagination .pagination { display: flex; gap: 4px; list-style: none; padding: 0; margin: 0; align-items: center; }
.cat-pagination .pagination .page-item { display: inline-block; margin: 0; }
.cat-pagination .pagination .page-link {
    display: flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 10px; font-size: 13px; font-weight: 500;
    color: #444; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px;
    text-decoration: none; transition: all 0.2s;
}
.cat-pagination .pagination .page-item.active .page-link { background: #3c7d17; border-color: #3c7d17; color: #fff; }
.cat-pagination .pagination .page-link:hover:not(.active) { background: #f5f5f5; color: #3c7d17; }

/* Mobile Drawer Overlay */
@media (max-width: 991px) {
    .mobile-filter-btn { display: inline-flex; }
    .cat-sidebar {
        position: fixed; top: 0; left: -300px; width: 280px; height: 100vh;
        z-index: 1050; background: #fff; overflow-y: auto;
        transition: left 0.3s ease; padding: 16px; box-shadow: 4px 0 20px rgba(0,0,0,0.15);
    }
    .cat-sidebar.show { left: 0; }
    .cat-sidebar-sticky { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
    .cat-sidebar-close { background: none; border: none; font-size: 24px; color: #999; cursor: pointer; }
    .cat-filter-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
    .cat-filter-overlay.show { display: block; }
}

.cat-empty {
    text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-empty i { font-size: 48px; color: #d0d0d0; margin-bottom: 16px; }
.cat-empty h4 { font-size: 18px; color: #333; font-weight: 600; margin-bottom: 8px; }
.cat-empty p { font-size: 13px; color: #888; margin-bottom: 20px; }
</style>
@endpush

@push('seo')
<meta name="app-url" content="{{ route('subcategory', $subcategory->slug) }}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $subcategory->meta_description }}" />
<meta name="keywords" content="{{ $subcategory->slug }}" />

<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="{{ $subcategory->subcategoryName }}" />
<meta name="twitter:title" content="{{ $subcategory->subcategoryName }}" />
<meta name="twitter:description" content="{{ $subcategory->meta_description }}" />
<meta name="twitter:image" content="{{ asset($subcategory->image) }}" />

<meta property="og:title" content="{{ $subcategory->subcategoryName }}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ route('subcategory', $subcategory->slug) }}" />
<meta property="og:image" content="{{ asset($subcategory->image) }}" />
<meta property="og:description" content="{{ $subcategory->meta_description }}" />
<meta property="og:site_name" content="{{ $subcategory->subcategoryName }}" />
@endpush

@section('content')

{{-- ───── Page Breadcrumb ───── --}}
<section class="pdp-header">
    <div class="container">
        <div class="pdp-header-inner">
            <div class="pdp-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                @if($subcategory->category)
                <span>›</span>
                <a href="{{ route('category', $subcategory->category->slug) }}">{{ $subcategory->category->name }}</a>
                @endif
                <span>›</span>
                <strong>{{ $subcategory->subcategoryName }}</strong>
            </div>
        </div>
    </div>
</section>

{{-- ───── Main Layout ───── --}}
<section class="cat-layout">
    <div class="container">
        {{-- Toolbar --}}
        <div class="cat-toolbar">
            <div class="cat-toolbar-left">
                <button class="mobile-filter-btn" id="mobileFilterOpen">
                    <i class="fa fa-filter"></i> Filters
                </button>
                <span class="showing-data">
                    Showing <strong>{{ $products->firstItem() ?? 0 }}</strong>–<strong>{{ $products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ number_format($products->total()) }}</strong> results for <strong>{{ $subcategory->subcategoryName }}</strong>
                </span>
            </div>
            <div class="cat-sort-wrapper">
                <label for="catSort">Sort by:</label>
                <form action="" class="sort-form" id="catSortForm">
                    <select name="sort" id="catSort" class="sort">
                        <option value="1" {{ request()->get('sort')==1 ? 'selected' : '' }}>Latest</option>
                        <option value="2" {{ request()->get('sort')==2 ? 'selected' : '' }}>Oldest</option>
                        <option value="3" {{ request()->get('sort')==3 ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="4" {{ request()->get('sort')==4 ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="5" {{ request()->get('sort')==5 ? 'selected' : '' }}>Name: A–Z</option>
                        <option value="6" {{ request()->get('sort')==6 ? 'selected' : '' }}>Name: Z–A</option>
                    </select>
                    <input type="hidden" name="min_price" value="{{ request()->get('min_price') }}" />
                    <input type="hidden" name="max_price" value="{{ request()->get('max_price') }}" />
                </form>
            </div>
        </div>

        <div class="row">
            {{-- ─── Sidebar ─── --}}
            <div class="col-lg-3">
                <div class="cat-sidebar" id="catSidebar">
                    <div class="cat-sidebar-sticky d-lg-none">
                        <h5>Filters</h5>
                        <button class="cat-sidebar-close" id="catSidebarClose">&times;</button>
                    </div>

                    {{-- Categories Tree --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarCategories">
                            Categories <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarCategories">
                            <ul class="cat-list">
                                @foreach($categories as $cat)
                                <li class="cat-item">
                                    @php $subs = $cat->subcategories ?? collect(); @endphp
                                    @if($subs->count() > 0)
                                    <button class="cat-toggle {{ $subs->contains('id', $subcategory->id) ? 'active' : '' }}" data-toggle="subcat" data-target="#catSub-{{ $cat->id }}">
                                        <span>{{ $cat->name }}</span>
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                    <ul class="cat-sub-list {{ $subs->contains('id', $subcategory->id) ? 'open' : '' }}" id="catSub-{{ $cat->id }}">
                                        <li class="cat-sub-item">
                                            <a href="{{ route('category', $cat->slug) }}" class="cat-sub-link">
                                                {{ $cat->name }} (All)
                                            </a>
                                        </li>
                                        @foreach($subs as $sub)
                                        <li class="cat-sub-item">
                                            <a href="{{ url('subcategory/'.$sub->slug) }}" class="cat-sub-link {{ $sub->id == $subcategory->id ? 'active' : '' }}">{{ $sub->subcategoryName }}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @else
                                    <a href="{{ route('category', $cat->slug) }}" class="cat-link">
                                        {{ $cat->name }}
                                    </a>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    {{-- Price Range Slider --}}
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarPrice">
                            Price Range <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarPrice">
                            <div class="cat-price-range">
                                <form action="" method="GET" id="catPriceForm">
                                    @if(request()->get('sort'))
                                    <input type="hidden" name="sort" value="{{ request()->get('sort') }}" />
                                    @endif
                                    <div class="price-inputs">
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="min_price" id="cat_price_min"
                                                   value="{{ request()->get('min_price', floor($globalMin ?? 0)) }}" readonly />
                                        </div>
                                        <span class="price-separator">—</span>
                                        <div class="price-input-group">
                                            <span>৳</span>
                                            <input type="text" name="max_price" id="cat_price_max"
                                                   value="{{ request()->get('max_price', ceil($globalMax ?? 10000)) }}" readonly />
                                        </div>
                                    </div>
                                    <div id="cat-price-range"></div>
                                    <div class="price-filter-actions">
                                        <button type="submit" class="btn-price-filter">Apply Filter</button>
                                        @if(request()->has('min_price') || request()->has('max_price'))
                                        <a href="{{ route('subcategory', $subcategory->slug) }}" class="btn-price-reset">Reset</a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Childcategory Filter --}}
                    @if(isset($childcategories) && $childcategories->count() > 0)
                    <div class="cat-sidebar-card">
                        <div class="cat-sidebar-title active" data-toggle="collapse" data-target="#catSidebarChildcat">
                            Sub-Type Filter <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="cat-sidebar-body" id="catSidebarChildcat">
                            <form action="" method="GET" id="childcatForm">
                                @if(request()->get('sort'))
                                <input type="hidden" name="sort" value="{{ request()->get('sort') }}" />
                                @endif
                                @if(request()->get('min_price'))
                                <input type="hidden" name="min_price" value="{{ request()->get('min_price') }}" />
                                <input type="hidden" name="max_price" value="{{ request()->get('max_price') }}" />
                                @endif
                                <ul class="cat-filter-list">
                                    @foreach($childcategories as $child)
                                    <li>
                                        <label class="cat-filter-label">
                                            <input type="checkbox" name="childcategory[]" value="{{ $child->id }}"
                                                   {{ in_array($child->id, (array)request()->get('childcategory', [])) ? 'checked' : '' }}
                                                   onchange="this.form.submit()" />
                                            <span class="filter-name">{{ $child->childcategoryName }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="cat-filter-overlay" id="catFilterOverlay"></div>
            </div>

            {{-- ─── Products Grid ─── --}}
            <div class="col-lg-9">
                @if($products->count() > 0)
                <div class="cat-products-grid">
                    @foreach($products as $product)
                        @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                    @endforeach
                </div>

                <div class="cat-pagination-wrap">
                    <div class="cat-pagination-info">
                        Showing <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
                        of <strong>{{ number_format($products->total()) }}</strong> results
                        @if($products->total() > 0)
                        <span style="color:#ccc;margin:0 8px;">|</span>
                        Page <strong>{{ $products->currentPage() }}</strong> of <strong>{{ $products->lastPage() }}</strong>
                        @endif
                    </div>
                    <div class="cat-pagination">
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @else
                <div class="cat-empty">
                    <i class="fa fa-box-open"></i>
                    <h4>No products found</h4>
                    <p>Try adjusting your filters or search criteria.</p>
                    <a href="{{ route('subcategory', $subcategory->slug) }}" class="btn-price-filter" style="display:inline-block;padding:10px 30px;text-decoration:none;">View All</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script src="{{ asset('frontEnd/js/jquery-ui.js') }}"></script>
<script>
    $(function() {
        // Price Slider
        var globalMin = {{ floor($globalMin ?? 0) }};
        var globalMax = {{ ceil($globalMax ?? 10000) }};
        var currentMin = {{ request()->get('min_price', floor($globalMin ?? 0)) }};
        var currentMax = {{ request()->get('max_price', ceil($globalMax ?? 10000)) }};

        if (globalMin === globalMax) { globalMax = globalMin + 1000; }

        $("#cat-price-range").slider({
            range: true,
            min: globalMin,
            max: globalMax,
            values: [currentMin, currentMax],
            slide: function(event, ui) {
                $("#cat_price_min").val(ui.values[0]);
                $("#cat_price_max").val(ui.values[1]);
            }
        });

        // Collapsible Sidebar Sections
        $('[data-toggle="collapse"]').on('click', function() {
            var target = $(this).data('target');
            $(target).toggleClass('hidden');
            $(this).toggleClass('active');
        });

        // Toggle Subcategory Lists
        $('[data-toggle="subcat"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $(target).toggleClass('open');
            $(this).toggleClass('active');
        });

        // Sort Form Submit
        $("#catSort").on('change', function() {
            $("#catSortForm").submit();
        });

        // Mobile Filter Drawer
        $("#mobileFilterOpen").on('click', function() {
            $("#catSidebar, #catFilterOverlay").addClass('show');
        });
        $("#catSidebarClose, #catFilterOverlay").on('click', function() {
            $("#catSidebar, #catFilterOverlay").removeClass('show');
        });
    });
</script>
@endpush