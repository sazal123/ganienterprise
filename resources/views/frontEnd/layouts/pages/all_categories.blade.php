@extends('frontEnd.layouts.master')
@section('title', 'All Categories - Gani Enterprise')

@push('css')
<style>
/* ───── All Categories Page Styling ───── */
.all-cat-page {
    background: #f8f8f6;
    padding: 40px 0 70px;
    min-height: 650px;
}

/* Page Header / Breadcrumb */
.all-cat-header {
    background: #000;
    padding: 32px 0 28px;
    position: relative;
    overflow: hidden;
}
.all-cat-header-inner { position: relative; z-index: 1; }
.all-cat-title {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px;
}
.all-cat-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.6);
}
.all-cat-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
.all-cat-breadcrumb a:hover { color: #C9A84C; }
.all-cat-breadcrumb strong { color: #C9A84C; font-weight: 600; }

/* Grid Layout */
.all-cat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
@media (max-width: 991px) {
    .all-cat-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
}
@media (max-width: 575px) {
    .all-cat-grid { grid-template-columns: 1fr; gap: 16px; }
}

/* Category Card */
.all-cat-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    border: 1px solid #eaeaea;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.all-cat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border-color: #d0d0d0;
}

/* Card Banner / Image */
.all-cat-img-wrapper {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: #1a1a2e;
}
.all-cat-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.all-cat-card:hover .all-cat-img {
    transform: scale(1.06);
}
.all-cat-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.1) 60%);
    display: flex;
    align-items: flex-end;
    padding: 16px 20px;
}
.all-cat-name {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

/* Subcategory List Body */
.all-cat-body {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.subcat-tree {
    list-style: none;
    padding: 0;
    margin: 0 0 16px;
}
.subcat-tree-item {
    margin-bottom: 12px;
}
.subcat-tree-item:last-child { margin-bottom: 0; }

.subcat-main-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #222;
    text-decoration: none;
    transition: color 0.2s;
}
.subcat-main-link:hover {
    color: #3c7d17;
}
.subcat-main-link i {
    font-size: 11px;
    color: #999;
}

/* Childcategories Chips */
.childcat-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;
    padding-left: 12px;
}
.childcat-chip {
    font-size: 12px;
    color: #666;
    background: #f4f4f2;
    padding: 3px 10px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
}
.childcat-chip:hover {
    background: #3c7d17;
    color: #fff;
}

/* Card Action Button */
.all-cat-footer {
    padding-top: 14px;
    border-top: 1px solid #f0f0f0;
}
.btn-explore-cat {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 10px 16px;
    background: #000;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.2s;
}
.btn-explore-cat:hover {
    background: #222;
    color: #fff;
}
</style>
@endpush

@section('content')

{{-- ───── Page Breadcrumb ───── --}}
<section class="all-cat-header">
    <div class="container">
        <div class="all-cat-header-inner">
            <h1 class="all-cat-title">All Categories</h1>
            <div class="all-cat-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>›</span>
                <strong>All Categories</strong>
            </div>
        </div>
    </div>
</section>

{{-- ───── Main Categories Grid ───── --}}
<section class="all-cat-page">
    <div class="container">
        @if(isset($categories) && $categories->count() > 0)
        <div class="all-cat-grid">
            @foreach($categories as $category)
            @php
                $catImg = $category->image;
                if ($catImg && str_starts_with($catImg, 'public/')) {
                    $catImg = substr($catImg, 7);
                }
            @endphp
            <div class="all-cat-card">
                <a href="{{ route('category', $category->slug) }}" class="all-cat-img-wrapper">
                    @if($category->image)
                        <img src="{{ asset($catImg) }}" alt="{{ $category->name }}" class="all-cat-img" />
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                            <span class="text-white fw-bold" style="font-size:36px; font-family:'Playfair Display',serif;">{{ mb_substr($category->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="all-cat-img-overlay">
                        <h3 class="all-cat-name">{{ $category->name }}</h3>
                    </div>
                </a>

                <div class="all-cat-body">
                    @if($category->subcategories && $category->subcategories->count() > 0)
                    <ul class="subcat-tree">
                        @foreach($category->subcategories as $sub)
                        <li class="subcat-tree-item">
                            <a href="{{ url('subcategory/'.$sub->slug) }}" class="subcat-main-link">
                                <i class="fa fa-angle-right"></i> {{ $sub->subcategoryName }}
                            </a>
                            @if($sub->childcategories && $sub->childcategories->count() > 0)
                            <div class="childcat-chips">
                                @foreach($sub->childcategories as $child)
                                <a href="{{ url('products/'.$child->slug) }}" class="childcat-chip">{{ $child->childcategoryName }}</a>
                                @endforeach
                            </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted small mb-3">Explore items in {{ $category->name }}.</p>
                    @endif

                    <div class="all-cat-footer">
                        <a href="{{ route('category', $category->slug) }}" class="btn-explore-cat">
                            <span>Explore {{ $category->name }}</span>
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fa fa-folder-open text-muted mb-3" style="font-size:48px;"></i>
            <h4 class="text-dark fw-bold">No Categories Found</h4>
            <a href="{{ route('home') }}" class="btn btn-dark mt-3 rounded-pill px-4">Back to Home</a>
        </div>
        @endif
    </div>
</section>

@endsection
