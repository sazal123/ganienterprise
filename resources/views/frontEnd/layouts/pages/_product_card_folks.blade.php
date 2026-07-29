<div class="gani-product-card">
    <div class="gani-product-img-wrap">
        <a href="{{ route('product', $product->slug) }}" class="d-block w-100 h-100">
            @php $mainImage = $product->image ? $product->image->image : 'frontEnd/img/default-product.jpg'; @endphp
            <img src="{{ asset($mainImage) }}"
                 alt="{{ $product->name }}"
                 class="w-100 h-100 object-fit-cover gani-product-img"
                 data-main-img="{{ asset($mainImage) }}" />
        </a>

        {{-- Badges --}}
        @if($product->old_price && $product->old_price > $product->new_price)
            @php $discount = round((($product->old_price - $product->new_price) / $product->old_price) * 100); @endphp
            <span class="gani-badge gani-badge-dark">{{ $discount }}% OFF</span>
        @else
            <span class="gani-badge gani-badge-gold">New</span>
        @endif

        {{-- Hover add-to-cart --}}
        <div class="gani-product-hover">
            @if($product->procolors->isEmpty() && $product->prosizes->isEmpty() && $product->stock > 0)
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}" />
                    <input type="hidden" name="qty" value="1" />
                    <button type="submit" class="gani-add-cart-btn">Add to Cart</button>
                </form>
            @else
                <a href="{{ route('product', $product->slug) }}" class="gani-add-cart-btn">Add to Cart</a>
            @endif
        </div>

        {{-- Stock out overlay --}}
        @if($product->stock < 1)
        <div class="gani-stock-overlay">Stock Out</div>
        @endif
    </div>

    <div class="gani-product-info">
        <a href="{{ route('product', $product->slug) }}">
            <h6 class="gani-product-name">{{ Str::limit($product->name, 50) }}</h6>
        </a>
        <div class="gani-product-price">
            @if($product->old_price && $product->old_price > $product->new_price)
                <span class="gani-old-price">৳{{ number_format($product->old_price) }}</span>
            @endif
            <span class="gani-new-price">৳{{ number_format($product->new_price) }}</span>
        </div>

        {{-- Color variant images --}}
        @if($product->procolors && $product->procolors->count() > 0)
        <div class="gani-color-swatches">
            @foreach($product->procolors->take(5) as $pc)
                @if($pc->color)
                    @php
                        $colorImage = $product->images->where('color_id', $pc->color_id)->first();
                        $thumbUrl = asset($colorImage ? $colorImage->image : $mainImage);
                    @endphp
                    <button type="button" class="gani-swatch-link gani-swatch-btn"
                            data-swap-img="{{ $thumbUrl }}"
                            title="{{ $pc->color->colorName ?? '' }}">
                        <img src="{{ $thumbUrl }}"
                             alt="{{ $pc->color->colorName ?? '' }}"
                             class="gani-swatch-img" />
                    </button>
                @endif
            @endforeach
            @if($product->procolors->count() > 5)
                <a href="{{ route('product', $product->slug) }}" class="gani-swatch-more">+{{ $product->procolors->count() - 5 }}</a>
            @endif
        </div>
        @endif
    </div>
</div>
