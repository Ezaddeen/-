<article class="product-card">
    <div class="product-image">
        <a href="{{ route('products.show', $product['slug']) }}" style="position:absolute;inset:0">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
        <span class="made-local">صورة توضيحية</span>
        </a>
        @auth
            <form method="POST" action="{{ route('favorites.toggle', $product['slug']) }}">
                @csrf
                <button class="product-heart favorite-toggle" type="submit" aria-label="أضف أو احذف {{ $product['name'] }} من المفضلة" data-product="{{ $product['slug'] }}">♡</button>
            </form>
        @else
            <a class="product-heart" href="{{ route('login') }}" aria-label="سجّل الدخول لإضافة {{ $product['name'] }} للمفضلة">♡</a>
        @endauth
    </div>
    <div class="product-info">
        <div class="product-meta"><span>{{ $product['category'] }}</span><span class="rating">★ {{ $product['rating'] }}</span></div>
        <h3><a href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }}</a></h3>
        <p class="product-maker">بواسطة <a href="{{ route('artisans.show', $product['artisan_slug']) }}">{{ $product['artisan'] }}</a> · {{ $product['city'] }}</p>
        <div class="product-bottom"><strong>{{ number_format($product['price']) }} <small>ر.ي</small></strong><form method="POST" action="{{ route('cart.add', $product['slug']) }}">@csrf<button class="quick-add" type="submit" aria-label="أضف {{ $product['name'] }} إلى السلة">+</button></form></div>
    </div>
</article>
{{-- بطاقة منتج مشتركة تعرض الصورة والاسم والسعر والحرفي وزر المفضلة والسلة. --}}