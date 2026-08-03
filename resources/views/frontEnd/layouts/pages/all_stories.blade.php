@extends('frontEnd.layouts.master')
@section('title', 'Stories That Lead')
@section('content')

<section class="gani-section gani-section-light" style="padding: 40px 0 60px;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gani-section-title">STORIES THAT LEAD</h2>
            <div class="gani-divider mx-auto"></div>
        </div>

        @if($all_stories->count() > 0)
        <div class="row g-4 justify-content-center">
            @foreach($all_stories as $story)
            @php
                $thumb = $story->thumbnail;
                if ($thumb && str_starts_with($thumb, 'public/')) { $thumb = substr($thumb, 7); }
                $video = $story->video;
                if ($video && str_starts_with($video, 'public/')) { $video = substr($video, 7); }
                $storyProduct = $story->product;
                $prodImg = $storyProduct && $storyProduct->image ? $storyProduct->image->image : '';
            @endphp
            <div class="col-6 col-md-g5 mb-4">
                <div class="gani-story-card"
                     data-video="{{ asset($video) }}"
                     data-thumb="{{ $thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg') }}"
                     data-prod-img="{{ $prodImg ? asset($prodImg) : '' }}"
                     data-prod-name="{{ $storyProduct ? $storyProduct->name : '' }}"
                     data-prod-price="{{ $storyProduct ? number_format($storyProduct->new_price) : '0' }}"
                     data-prod-old="{{ $storyProduct && $storyProduct->old_price ? number_format($storyProduct->old_price) : '' }}"
                     data-prod-slug="{{ $storyProduct ? $storyProduct->slug : '#' }}"
                     data-prod-id="{{ $storyProduct ? $storyProduct->id : '' }}"
                     data-prod-stock="{{ $storyProduct ? $storyProduct->stock : 0 }}"
                     data-prod-link="{{ $storyProduct ? route('product', $storyProduct->slug) : '#' }}"
                     data-add-to-cart="{{ $storyProduct && $storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0 ? route('cart.store') : '' }}">
                    <div class="gani-story-thumb-wrap">
                        <video src="{{ asset($video) }}" class="gani-story-video" muted playsinline loop autoplay preload="auto" poster="{{ $thumb ? asset($thumb) : asset('frontEnd/img/default-product.jpg') }}"></video>
                        <div class="gani-story-play-indicator"><i class="fa-solid fa-volume-xmark"></i></div>
                    </div>
                    <div class="gani-story-info">
                        @if($storyProduct)
                            <div class="gani-story-product-row">
                                @if($prodImg)
                                <img src="{{ asset($prodImg) }}" class="gani-story-prod-img" />
                                @endif
                                <div class="gani-story-prod-details">
                                    <h6 class="gani-story-prod-name">{{ Str::limit($storyProduct->name, 35) }}</h6>
                                    <span class="gani-story-prod-price">৳{{ number_format($storyProduct->new_price) }}</span>
                                </div>
                            </div>
                            @if($storyProduct->procolors->isEmpty() && $storyProduct->prosizes->isEmpty() && $storyProduct->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $storyProduct->id }}" />
                                <input type="hidden" name="qty" value="1" />
                                <button type="submit" class="gani-story-cart-btn">Add To Cart</button>
                            </form>
                            @else
                            <a href="{{ route('product', $storyProduct->slug) }}" class="gani-story-cart-btn">Add To Cart</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="cat-pagination-wrap mt-4" style="background:#fff; padding: 16px 20px; border-radius: 8px;">
            <div class="cat-pagination-info">
                Showing <strong>{{ $all_stories->firstItem() }}</strong> to <strong>{{ $all_stories->lastItem() }}</strong> of <strong>{{ $all_stories->total() }}</strong> stories
            </div>
            <div class="cat-pagination">
                {{ $all_stories->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <p class="text-muted">No stories available at the moment.</p>
        </div>
        @endif
    </div>
</section>

{{-- STORY PRODUCT MODAL — Quick view with video --}}
<div class="gani-video-modal" id="ganiStoryModal">
    <button class="gani-video-close" onclick="closeStoryModal()">&times;</button>
    <div class="gani-story-modal-inner">
        <div class="gani-story-video-col">
            <video id="modalStoryVideo" controls playsinline loop></video>
        </div>
        <div class="gani-story-product-col" id="modalStoryProductCol">
            <div class="gani-story-modal-prod">
                <img id="modalProdImg" src="" class="gani-modal-prod-img" />
                <div class="gani-modal-prod-info">
                    <h5 id="modalProdName"></h5>
                    <div class="gani-modal-prices">
                        <span id="modalProdPrice" class="current-price"></span>
                        <del id="modalProdOldPrice" class="old-price"></del>
                    </div>
                </div>
            </div>
            <div class="gani-story-modal-actions">
                <a id="modalProdLink" href="#" class="gani-btn-secondary w-100 mb-2 text-center">View Product Details</a>
                <div id="modalCartContainer"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
function openStoryModal(card) {
    var videoSrc = card.data('video');
    var prodImg = card.data('prod-img');
    var prodName = card.data('prod-name');
    var prodPrice = card.data('prod-price');
    var prodOld = card.data('prod-old');
    var prodLink = card.data('prod-link');
    var prodId = card.data('prod-id');
    var addToCartUrl = card.data('add-to-cart');

    var modal = $('#ganiStoryModal');
    var video = $('#modalStoryVideo')[0];

    video.src = videoSrc;
    video.play();

    if (prodName) {
        $('#modalProdImg').attr('src', prodImg).toggle(!!prodImg);
        $('#modalProdName').text(prodName);
        $('#modalProdPrice').text('৳' + prodPrice);
        $('#modalProdOldPrice').text(prodOld ? '৳' + prodOld : '').toggle(!!prodOld);
        $('#modalProdLink').attr('href', prodLink);

        var cartHtml = '';
        if (addToCartUrl && prodId) {
            cartHtml = '<form action="' + addToCartUrl + '" method="POST">' +
                       '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                       '<input type="hidden" name="id" value="' + prodId + '">' +
                       '<input type="hidden" name="qty" value="1">' +
                       '<button type="submit" class="gani-btn-primary w-100">Add To Cart</button>' +
                       '</form>';
        } else {
            cartHtml = '<a href="' + prodLink + '" class="gani-btn-primary w-100 text-center">Buy Now</a>';
        }
        $('#modalCartContainer').html(cartHtml);
        $('#modalStoryProductCol').show();
    } else {
        $('#modalStoryProductCol').hide();
    }

    modal.addClass('active');
    $('body').css('overflow', 'hidden');
}

function closeStoryModal() {
    var modal = $('#ganiStoryModal');
    var video = $('#modalStoryVideo')[0];
    video.pause();
    video.src = '';
    modal.removeClass('active');
    $('body').css('overflow', '');
}

$(document).ready(function() {
    $('.gani-story-video').each(function() {
        this.muted = true;
        var p = this.play();
        if (p !== undefined) {
            p.catch(function(e) {});
        }
    });

    $('.gani-story-card').on('click', function(e) {
        if ($(e.target).closest('form, button, a').length) return;
        openStoryModal($(this));
    });

    $('#ganiStoryModal').on('click', function(e) {
        if ($(e.target).is('#ganiStoryModal')) {
            closeStoryModal();
        }
    });
});
</script>
@endpush
