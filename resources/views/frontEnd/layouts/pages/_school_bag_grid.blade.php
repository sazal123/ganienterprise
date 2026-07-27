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
                <div class="sb-card">
                    <div class="sb-card-img-wrap">
                        <a href="{{ route('product', $product->slug) }}">
                            <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" class="sb-card-img" id="img-{{ $product->id }}" />
                        </a>
                        @if($discount > 0)
                            <span class="sb-badge-discount">-{{ $discount }}%</span>
                        @else
                            <span class="sb-badge-new">NEW</span>
                        @endif

                        {{-- Quick Order Hover Button --}}
                        <div class="sb-card-actions">
                            <button type="button" 
                                    class="sb-quick-order-btn open-quick-modal" 
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->new_price }}"
                                    data-oldprice="{{ $product->old_price }}"
                                    data-img="{{ asset($mainImage) }}">
                                <i class="fa fa-shopping-bag me-1"></i> অর্ডার করুন
                            </button>
                        </div>
                    </div>

                    <div class="sb-card-body">
                        <a href="{{ route('product', $product->slug) }}" class="sb-card-title-link">
                            <h3 class="sb-card-title">{{ Str::limit($product->name, 45) }}</h3>
                        </a>

                        <div class="sb-card-rating">
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <i class="fa fa-star text-warning"></i>
                            <span class="sb-review-count">(4.9/5)</span>
                        </div>

                        <div class="sb-card-price-wrap">
                            <span class="sb-card-price">৳{{ number_format($product->new_price) }}</span>
                            @if($product->old_price && $product->old_price > $product->new_price)
                                <del class="sb-card-old-price">৳{{ number_format($product->old_price) }}</del>
                            @endif
                        </div>

                        {{-- Quick Buy Direct Button --}}
                        <a href="{{ route('product', $product->slug) }}" class="sb-btn-details">
                            বিস্তারিত দেখুন <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center mt-5 sb-pagination-wrap">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
@else
    <div class="text-center py-5">
        <img src="{{ asset('frontEnd/img/no-product.png') }}" alt="No products" style="max-width: 120px; opacity: 0.7;" class="mb-3">
        <h4 class="fw-bold text-muted">কোনো স্কুল ব্যাগ পাওয়া যায়নি</h4>
        <p class="text-secondary mb-3">অনুগ্রহ করে ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন।</p>
        <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-reset-filters">সকল ব্যাগ দেখুন</button>
    </div>
@endif
