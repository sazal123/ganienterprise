@extends('frontEnd.layouts.master')
@section('title', 'Hot Deals / Trending Collection')
@push('css')
<link rel="stylesheet" href="{{ asset('frontEnd/css/jquery-ui.css') }}" />
<style>
/* ───── Layout ───── */
.cat-layout { padding: 30px 0 60px; background: #f8f8f6; min-height: 600px; }

/* ───── Toolbar ───── */
.cat-toolbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    background: #fff; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.cat-toolbar-left { display: flex; align-items: center; gap: 16px; }
.cat-toolbar-left .showing-data { font-size: 13px; color: #666; }
.mobile-filter-btn {
    display: none; align-items: center; gap: 6px;
    padding: 7px 16px; background: #3c7d17; color: #fff; border: none;
    border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;
}
.mobile-filter-btn:hover { background: #2d5d11; }

.cat-sort-wrapper { display: flex; align-items: center; gap: 8px; }
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

.cat-toggle {
    background: none; border: none; padding: 10px 18px; width: 100%; text-align: left;
    font-size: 13px; color: #444; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between;
}
.cat-toggle:hover { background: #f8f8f6; color: #3c7d17; }
.cat-toggle i { font-size: 10px; color: #bbb; transition: transform 0.3s; }
.cat-toggle.active i { transform: rotate(90deg); }

.cat-sub-list { list-style: none; padding: 0 0 4px 20px; margin: 0; display: none; }
.cat-sub-list.open { display: block; }
.cat-sub-link {
    display: block; padding: 8px 18px 8px 16px; font-size: 12px; color: #666;
    text-decoration: none; border-left: 2px solid transparent; transition: all 0.2s;
}
.cat-sub-link:hover { color: #3c7d17; border-left-color: #3c7d17; background: rgba(60,125,23,0.03); }

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
.cat-pagination-info { font-size: 13px; color: #666; }
.cat-pagination-info strong { color: #1a1a2e; }

.cat-pagination .pagination { display: flex; align-items: center; gap: 4px; margin: 0; list-style: none; }
.cat-pagination .page-item { margin: 0; }
.cat-pagination .page-link {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 12px;
    border: 1px solid #e5e5e5; border-radius: 6px;
    font-size: 13px; font-weight: 500; color: #444; background: #fff;
    text-decoration: none; transition: all 0.2s ease;
}
.cat-pagination .page-link:hover { border-color: #C9A84C; color: #C9A84C; background: rgba(201,168,76,0.05); }
.cat-pagination .active .page-link { border-color: #C9A84C; background: #C9A84C; color: #fff; font-weight: 600; }
.cat-pagination .disabled span.page-link { border: none; background: transparent; color: #bbb; pointer-events: none; }

/* ───── Empty ───── */
.cat-empty { text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px; }
.cat-empty i { font-size: 48px; color: #ddd; margin-bottom: 16px; }
.cat-empty h4 { font-size: 18px; color: #333; margin-bottom: 8px; }
.cat-empty p { color: #888; font-size: 14px; }

/* ───── Mobile ───── */
.cat-filter-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
.cat-filter-overlay.active { display: block; }

@media (max-width: 767px) {
    .mobile-filter-btn { display: flex; }
    .cat-sidebar {
        position: fixed; top: 0; left: 0; bottom: 0; width: 300px; max-width: 85vw;
        background: #fff; z-index: 9999; transform: translateX(-100%);
        transition: transform 0.35s ease; overflow-y: auto; padding: 0;
        box-shadow: 2px 0 20px rgba(0,0,0,0.15);
    }
    .cat-sidebar.active { transform: translateX(0); }
    .cat-sidebar-card { border-radius: 0; box-shadow: none; margin-bottom: 0; border-bottom: 1px solid #f0f0f0; }
    .cat-sidebar-sticky {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; background: #fff; border-bottom: 1px solid #eee;
        position: sticky; top: 0; z-index: 2;
    }
    .cat-sidebar-sticky h5 { font-size: 15px; font-weight: 600; margin: 0; }
    .cat-sidebar-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; }
    .cat-toolbar { padding: 12px 14px; }
}
</style>
@endpush

@section('content')

{{-- ───── Main Layout ───── --}}
<section class="cat-layout">
    <div class="container">
        {{-- Toolbar --}}
        <div class="cat-toolbar">
            <div class="cat-toolbar-left">
                <button class="mobile-filter-btn" id="catMobileFilterToggle">
                    <i class="fa fa-sliders-h"></i> Filter
                </button>
                <div class="showing-data">
                    Showing <strong>{{ $products->firstItem() ?? 0 }}</strong>–<strong>{{ $products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $products->total() }}</strong> results
                </div>
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

                    {{-- Categories --}}
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
                                    <button class="cat-toggle" data-toggle="subcat" data-target="#catSub-{{ $cat->id }}">
                                        <span>{{ $cat->name }}</span>
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                    <ul class="cat-sub-list" id="catSub-{{ $cat->id }}">
                                        <li class="cat-sub-item">
                                            <a href="{{ route('category', $cat->slug) }}" class="cat-sub-link">
                                                {{ $cat->name }} (All)
                                            </a>
                                        </li>
                                        @foreach($subs as $sub)
                                        <li class="cat-sub-item">
                                            <a href="{{ url('subcategory/'.$sub->slug) }}" class="cat-sub-link">{{ $sub->subcategoryName }}</a>
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

                    {{-- Price Range --}}
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
                                        <button type="submit" class="btn-price-filter">Apply</button>
                                        <a href="{{ route('hotdeals') }}" class="btn-price-reset">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cat-filter-overlay" id="catFilterOverlay"></div>
            </div>

            {{-- ─── Products ─── --}}
            <div class="col-lg-9">
                @if($products->count() > 0)
                <div class="cat-products-grid">
                    @foreach($products as $product)
                        @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
                    @endforeach
                </div>
                <div class="cat-pagination-wrap">
                    <div class="cat-pagination-info">
                        Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> products
                    </div>
                    <div class="cat-pagination">
                        {{ $products->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @else
                <div class="cat-empty">
                    <i class="fa fa-shopping-bag"></i>
                    <h4>No Products Found</h4>
                    <p>We couldn't find any products in Hot Deals matching your criteria.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script src="{{ asset('frontEnd/css/jquery-ui.js') }}"></script>
<script>
$(document).ready(function() {
    // Sort change
    $('#catSort').on('change', function() {
        $('#catSortForm').submit();
    });

    // Mobile filter toggle
    $('#catMobileFilterToggle').on('click', function() {
        $('#catSidebar').addClass('active');
        $('#catFilterOverlay').addClass('active');
        $('body').css('overflow', 'hidden');
    });
    $('#catSidebarClose, #catFilterOverlay').on('click', function() {
        $('#catSidebar').removeClass('active');
        $('#catFilterOverlay').removeClass('active');
        $('body').css('overflow', '');
    });

    // Collapsible cards
    $('[data-toggle="collapse"]').on('click', function() {
        var target = $($(this).data('target'));
        $(this).toggleClass('active');
        target.toggleClass('hidden');
    });

    // Subcategory toggle
    $('[data-toggle="subcat"]').on('click', function() {
        var target = $($(this).data('target'));
        $(this).toggleClass('active');
        target.toggleClass('open');
    });

    // jQuery UI Price Slider
    var minVal = parseInt("{{ request()->get('min_price', floor($globalMin ?? 0)) }}");
    var maxVal = parseInt("{{ request()->get('max_price', ceil($globalMax ?? 10000)) }}");
    var absMin = parseInt("{{ floor($globalMin ?? 0) }}");
    var absMax = parseInt("{{ ceil($globalMax ?? 10000) }}");

    if (absMin >= absMax) { absMin = 0; absMax = 10000; }

    $("#cat-price-range").slider({
        range: true,
        min: absMin,
        max: absMax,
        values: [minVal, maxVal],
        slide: function(event, ui) {
            $("#cat_price_min").val(ui.values[0]);
            $("#cat_price_max").val(ui.values[1]);
        }
    });
});
</script>
@endpush