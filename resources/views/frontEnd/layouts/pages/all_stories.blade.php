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

{{-- ============================================================ --}}
{{-- STORY PRODUCT MODAL — Quick view with video (Exact same as index) --}}
{{-- ============================================================ --}}
<div class="gani-video-modal" id="ganiStoryModal">
    <button class="gani-video-close" onclick="closeStoryModal()">&times;</button>
    <div class="gani-story-modal-inner">
        <div class="gani-story-modal-left">
            <video id="ganiStoryVideo" controls playsinline></video>
        </div>
        <div class="gani-story-modal-right">
            <h3 class="gani-sm-title" id="ganiSmTitle">Product Name</h3>
            <div class="gani-sm-price-row">
                <span class="gani-sm-price" id="ganiSmPrice">৳0</span>
                <span class="gani-sm-old-price" id="ganiSmOldPrice"></span>
            </div>
            <div class="gani-sm-colors" id="ganiSmColors" style="display:none;">
                <span class="gani-sm-color-label">Color: <strong id="ganiSmColorName">Brown</strong></span>
                <div class="gani-sm-color-swatches" id="ganiSmColorSwatches"></div>
            </div>
            <form id="ganiSmCartForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="ganiSmProdId" value="" />
                <input type="hidden" name="qty" value="1" />
                <button type="submit" class="gani-sm-cart-btn" id="ganiSmCartBtn">Add To Cart</button>
            </form>
            <a href="#" class="gani-sm-view-link" id="ganiSmViewLink">View Full Details →</a>
        </div>
    </div>
</div>

@endsection

@push('script')
<style>
    #ganiSmViewLink {
    background: #000;
    color: #fff;
    padding: 5px;
}
</style>
<script>
    // Story product modal - Exact same logic as index page
    function openStoryModal(card) {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        var videoSrc = card.getAttribute('data-video');
        var thumbSrc = card.getAttribute('data-thumb');
        video.src = videoSrc;
        video.setAttribute('poster', thumbSrc);
        video.onerror = function() {
            video.setAttribute('poster', thumbSrc);
        };
        document.getElementById('ganiSmTitle').textContent = card.getAttribute('data-prod-name');
        document.getElementById('ganiSmPrice').textContent = '৳' + card.getAttribute('data-prod-price');
        var oldEl = document.getElementById('ganiSmOldPrice');
        var oldPrice = card.getAttribute('data-prod-old');
        oldEl.textContent = oldPrice ? '৳' + oldPrice : '';
        oldEl.style.display = oldPrice ? 'inline' : 'none';
        document.getElementById('ganiSmProdId').value = card.getAttribute('data-prod-id');
        var cartForm = document.getElementById('ganiSmCartForm');
        var cartAction = card.getAttribute('data-add-to-cart');
        if (cartAction) {
            cartForm.action = cartAction;
            document.getElementById('ganiSmCartBtn').style.display = 'block';
        } else {
            cartForm.action = '';
            document.getElementById('ganiSmCartBtn').style.display = 'none';
        }
        document.getElementById('ganiSmViewLink').href = card.getAttribute('data-prod-link');
        modal.classList.add('active');
        video.load();
        video.play().catch(function() {});
    }

    function closeStoryModal() {
        var modal = document.getElementById('ganiStoryModal');
        var video = document.getElementById('ganiStoryVideo');
        modal.classList.remove('active');
        video.pause();
        video.src = '';
    }

    document.addEventListener('click', function(e) {
        var modal = document.getElementById('ganiStoryModal');
        if (modal && e.target === modal) closeStoryModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStoryModal();
    });

    $(document).ready(function() {
        // Autoplay muted videos
        $('.gani-story-video').each(function() {
            this.muted = true;
            var p = this.play();
            if (p !== undefined) {
                p.catch(function(e) {});
            }
        });

        // Bind click on story cards
        document.querySelectorAll('.gani-story-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                if (e.target.closest('.gani-story-cart-btn') || e.target.closest('form')) return;
                openStoryModal(this);
            });
        });
    });
</script>
@endpush
