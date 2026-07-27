@if($products->count() > 0)
    <div class="row g-4">
        @foreach($products as $product)
            @php 
                $mainImage = $product->image ? $product->image->image : 'frontEnd/img/default-product.jpg'; 
                $discount = ($product->old_price && $product->old_price > $product->new_price) 
                    ? round((($product->old_price - $product->new_price) / $product->old_price) * 100) 
                    : 0;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="cmp-card">
                    <div class="cmp-card-img-wrap">
                        <a href="{{ route('product', $product->slug) }}">
                            <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" class="cmp-card-img" id="img-{{ $product->id }}" />
                        </a>
                        @if($discount > 0)
                            <span class="cmp-badge-discount">-{{ $discount }}%</span>
                        @else
                            <span class="cmp-badge-new">SPECIAL</span>
                        @endif

                        {{-- Quick Order Hover Button --}}
                        <div class="cmp-card-actions">
                            <button type="button" 
                                    class="cmp-quick-order-btn open-quick-modal" 
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->new_price }}"
                                    data-oldprice="{{ $product->old_price }}"
                                    data-img="{{ asset($mainImage) }}">
                                <i class="fa fa-shopping-bag me-1"></i> সরাসরি অর্ডার
                            </button>
                        </div>
                    </div>

                    <div class="cmp-card-body">
                        <a href="{{ route('product', $product->slug) }}" class="cmp-card-title-link">
                            <h3 class="cmp-card-title">{{ Str::limit($product->name, 45) }}</h3>
                        </a>

                        <div class="cmp-card-rating">
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <span class="cmp-review-count">(5.0)</span>
                        </div>

                        <div class="cmp-card-price-wrap">
                            <span class="cmp-card-price">৳{{ number_format($product->new_price) }}</span>
                            @if($product->old_price && $product->old_price > $product->new_price)
                                <del class="cmp-card-old-price">৳{{ number_format($product->old_price) }}</del>
                            @endif
                        </div>

                        <a href="{{ route('product', $product->slug) }}" class="cmp-btn-details">
                            বিস্তারিত দেখুন <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center mt-5 cmp-pagination-wrap">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="text-center py-5">
        <img src="{{ asset('frontEnd/img/no-product.png') }}" alt="No products" style="max-width: 120px; opacity: 0.7;" class="mb-3">
        <h4 class="fw-bold text-muted">কোনো প্রোডাক্ট পাওয়া যায়নি</h4>
        <p class="text-secondary mb-3">অনুগ্রহ করে ফিল্টার পরিবর্তন করে চেষ্টা করুন।</p>
        <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-reset-filters">সকল প্রোডাক্ট দেখুন</button>
    </div>
@endif
