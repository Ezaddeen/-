// سلوك واجهة المتجر التفاعلي للمفضلة ومعاينة صور المنتجات.
// يربط أزرار المفضلة في بطاقات المنتجات ويحدث حالة حالة الصفحة عند فتحها.
const initializeFavorites = () => {
    const syncFavoritePage = () => {
        const cards = document.querySelectorAll('[data-favorite-card]');
        if (!cards.length) return;
        const emptyState = document.querySelector('.favorites-empty');
        if (emptyState) emptyState.hidden = cards.length > 0;
    };

    document.querySelectorAll('.favorite-toggle').forEach((button) => {
        const product = button.dataset.product;
        const updateButton = (isFavorite) => {
            button.setAttribute('aria-pressed', String(isFavorite));
            button.textContent = isFavorite ? '♥' : '♡';
            button.style.color = isFavorite ? '#ad5941' : '#fff';
            button.setAttribute('aria-label', isFavorite ? 'أزل المنتج من المفضلة' : 'أضف المنتج إلى المفضلة');
        };

        updateButton(button.textContent.trim() === '♥');
    });

    syncFavoritePage();
};

// يعاين صورة المنتج الجديدة ويعرض حالة تحميل أو خطأ أو نجاح واضح للبائع.
const initializeProductImagePreview = () => {
    const input = document.querySelector('[data-product-image-input]');
    const preview = document.querySelector('[data-product-image-preview]');
    const status = document.querySelector('[data-product-image-status]');
    if (!input || !preview || !status) return;

    const updatePreview = () => {
        const source = input.value.trim();
        if (!source) {
            preview.hidden = true;
            status.textContent = 'أدخل رابط صورة لمعاينتها.';
            return;
        }

        preview.hidden = true;
        status.textContent = 'جار تحميل معاينة الصورة...';
        preview.src = source;
    };

    preview.addEventListener('load', () => {
        preview.hidden = false;
        status.textContent = 'تحقق من أن الصورة تعرض المنتج الذي وصفته.';
    });
    preview.addEventListener('error', () => {
        preview.hidden = true;
        status.textContent = 'تعذر تحميل الصورة. تحقق من الرابط وجرب صورة أخرى.';
    });
    input.addEventListener('change', updatePreview);
    input.addEventListener('blur', updatePreview);
    updatePreview();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeFavorites();
        initializeProductImagePreview();
    }, { once: true });
} else {
    initializeFavorites();
    initializeProductImagePreview();
}