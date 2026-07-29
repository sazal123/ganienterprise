@if($products->count() > 0)
    <div class="row g-3 g-md-4">
        @foreach($products as $product)
            @php 
                $mainImage = $product->image ? $product->image->image : 'frontEnd/img/default-product.jpg'; 
                $discount = ($product->old_price && $product->old_price > $product->new_price) 
                    ? round((($product->old_price - $product->new_price) / $product->old_price) * 100) 
                    : 0;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="cmp-card">
                    <!-- Image Container -->
                    <div class="cmp-card-img-box">
                        <a href="{{ route('product', $product->slug) }}">
                            <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" class="cmp-card-img" id="img-{{ $product->id }}" />
                        </a>
                        @if($discount > 0)
                            <span class="cmp-card-badge">-{{ $discount }}%</span>
                        @else
                            <span class="cmp-card-badge" style="background: #d97706;">SPECIAL</span>
                        @endif
                    </div>

                    <!-- Color Swatches Row -->
                    <div class="cmp-color-swatches">
                        <span class="cmp-color-dot" style="background: #047857;"></span>
                        <span class="cmp-color-dot" style="background: #d97706;"></span>
                        <span class="cmp-color-dot" style="background: #dc2626;"></span>
                        <span class="cmp-color-dot" style="background: #2563eb;"></span>
                    </div>

                    <!-- Product Title -->
                    <a href="{{ route('product', $product->slug) }}" class="cmp-card-title-link">
                        <h3 class="cmp-card-title" title="{{ $product->name }}">{{ Str::limit($product->name, 40) }}</h3>
                    </a>

                    <!-- Pricing Row -->
                    <div class="cmp-card-prices">
                        @if($product->old_price && $product->old_price > $product->new_price)
                            <del class="cmp-card-old-price">৳{{ number_format($product->old_price) }}</del>
                        @endif
                        <span class="cmp-card-new-price">৳{{ number_format($product->new_price) }}</span>
                    </div>

                    <!-- Order Action Button -->
                    <button type="button" 
                            class="cmp-btn-order open-quick-modal" 
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->new_price }}"
                            data-oldprice="{{ $product->old_price }}"
                            data-img="{{ asset($mainImage) }}">
                        <i class="fa fa-shopping-bag"></i> Order Now
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-5 cmp-pagination-wrap">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="text-center py-5 bg-white rounded-4 shadow-sm my-4">
        <img src="{{ asset('frontEnd/img/no-product.png') }}" alt="No products" style="max-width: 110px; opacity: 0.6;" class="mb-3">
        <h4 class="fw-bold text-dark mb-2">No Products Found</h4>
        <p class="text-muted mb-3 fs-6">Please try changing filters or search terms.</p>
        <button type="button" class="btn btn-success rounded-pill px-4 btn-reset-filters fw-bold">View All Products</button>
    </div>
@endif
