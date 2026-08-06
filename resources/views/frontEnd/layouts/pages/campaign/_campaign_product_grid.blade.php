@if($products->count() > 0)
    <div class="shop-products-grid mb-4">
        @foreach($products as $product)
            @include('frontEnd.layouts.pages._product_card_folks', ['product' => $product])
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="shop-pagination-wrap">
        <div class="shop-pagination-info">
            Showing <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
            of <strong>{{ number_format($products->total()) }}</strong> results
            @if($products->total() > 0)
            <span style="color:#ccc;margin:0 8px;">|</span>
            Page <strong>{{ $products->currentPage() }}</strong> of <strong>{{ $products->lastPage() }}</strong>
            @endif
        </div>
        <div class="shop-pagination cmp-pagination-wrap">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@else
    <div class="shop-empty">
        <i class="fa fa-box-open"></i>
        <h4>No products found</h4>
        <p>Try adjusting your filters or search criteria.</p>
        <button type="button" class="btn-reset-filters border-0" style="display:inline-block;padding:10px 30px;background:#C9A84C;color:#000;font-weight:700;border-radius:6px;cursor:pointer;">View All Products</button>
    </div>
@endif
