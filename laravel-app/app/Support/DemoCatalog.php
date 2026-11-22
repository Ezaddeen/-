<?php

namespace App\Support;

// يوفر هذا الملف بيانات عرض ثابتة للمنتجات والحرفيين ويستخدمها المتجر والاختبارات والبيع التجريبي.
class DemoCatalog
{
    public function categories(): array
    {
        return [
            ['name' => 'فخار وخزف', 'icon' => '◒', 'count' => 2],
            ['name' => 'نسيج وتطريز', 'icon' => '⌘', 'count' => 1],
            ['name' => 'فضيات وحلي', 'icon' => '✳', 'count' => 1],
            ['name' => 'بن وعسل', 'icon' => '◉', 'count' => 1],
            ['name' => 'منتجات موسمية', 'icon' => '⌁', 'count' => 1],
        ];
    }

    public function products(): array
    {
        return [
            [
                'slug' => 'blue-rim-plates',
                'name' => 'أطباق خزفية بحواف زرقاء',
                'category' => 'فخار وخزف',
                'price' => 8500,
                'city' => 'صنعاء القديمة',
                'artisan' => 'ورشة طين',
                'artisan_slug' => 'warshat-teen',
                'rating' => '4.9',
                'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=900&q=85',
                'description' => 'مجموعة أطباق خزفية متراصة بسطح فاتح وحواف زرقاء، بتصميم بسيط يناسب تقديم الطعام.',
                'story' => 'تمنح الحواف الزرقاء والأشكال المتناسقة طقم الأطباق مظهرًا هادئًا على المائدة.',
                'stock' => 4,
            ],
            [
                'slug' => 'sanaani-shawl',
                'name' => 'نسيج مخطط بخيوط ملونة',
                'category' => 'نسيج وتطريز',
                'price' => 14500,
                'city' => 'صنعاء',
                'artisan' => 'خيوط سارة',
                'artisan_slug' => 'khuyoot-sarah',
                'rating' => '5.0',
                'image' => 'https://images.unsplash.com/photo-1791323452838-0dd7c4db6df6?auto=format&fit=crop&w=900&q=85',
                'description' => 'قطعة نسيج مخططة بخيوط حمراء وزرقاء وبرتقالية وبيضاء، وتظهر فيها تفاصيل النسج بوضوح.',
                'story' => 'تتقاطع ألوان الخيوط في خطوط متتابعة، وتبرز خامة النسيج وتفاصيله عن قرب.',
                'stock' => 7,
            ],
            [
                'slug' => 'fresh-watermelon',
                'name' => 'شريحة بطيخ أحمر طازجة',
                'category' => 'منتجات موسمية',
                'price' => 22000,
                'city' => 'تهامة',
                'artisan' => 'سوق تهامة',
                'artisan_slug' => 'souq-tihama',
                'rating' => '4.8',
                'image' => 'https://images.unsplash.com/photo-1630081015918-8a21078e5cee?auto=format&fit=crop&w=900&q=85',
                'description' => 'شريحة بطيخ حمراء ظاهرة اللب والبذور، ملتقطة عن قرب على خلفية فاتحة.',
                'story' => 'تُظهر الصورة لون اللب الأحمر والبذور الداكنة في شريحة طازجة.',
                'stock' => 5,
            ],
            [
                'slug' => 'silver-earrings',
                'name' => 'أقراط فضة بنقش يافع',
                'category' => 'فضيات وحلي',
                'price' => 18500,
                'city' => 'يافع',
                'artisan' => 'فضة ونقش',
                'artisan_slug' => 'fidda-wa-naqsh',
                'rating' => '4.9',
                'image' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=85',
                'description' => 'زوج أقراط متدلية بإطار فضي وأحجار زرقاء وشفافة، موضوع على خلفية خضراء.',
                'story' => 'يجمع تصميم الأقراط بين الإطار الفضي والأحجار الزرقاء والشفافة الظاهرة في الصورة.',
                'stock' => 3,
            ],
            [
                'slug' => 'latte-art-coffee',
                'name' => 'قهوة لاتيه في أكواب خزفية',
                'category' => 'بن وعسل',
                'price' => 12000,
                'city' => 'صنعاء',
                'artisan' => 'مجلس البن',
                'artisan_slug' => 'majlis-albun',
                'rating' => '4.9',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85',
                'description' => 'أكواب قهوة لاتيه برغوة مزينة برسومات دائرية، إلى جانب كوب قهوة سوداء.',
                'story' => 'تظهر رسومات الرغوة في كل كوب بتفاصيل مختلفة تُحضّر وقت التقديم.',
                'stock' => 6,
            ],
        ];
    }

    public function artisans(): array
    {
        return [
            [
                'slug' => 'warshat-teen',
                'name' => 'ورشة طين',
                'craft' => 'فخار وخزف يدوي',
                'city' => 'صنعاء القديمة',
                'image' => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=1000&q=85',
                'story' => 'ورشة عائلية صغيرة تعيد تقديم الفخار اليمني بقطع للاستخدام اليومي، مع الحفاظ على وقت الصنعة وأثر اليد.',
            ],
            [
                'slug' => 'khuyoot-sarah',
                'name' => 'خيوط سارة',
                'craft' => 'نسيج وتطريز',
                'city' => 'صنعاء',
                'image' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1000&q=85',
                'story' => 'مشروع منزلي يحول الخيوط والأقمشة إلى قطع مطرزة بعناية، ويقبل بعض الطلبات الخاصة حسب الوقت المتاح.',
            ],
            [
                'slug' => 'majlis-albun',
                'name' => 'مجلس البن',
                'craft' => 'قهوة ومشروبات',
                'city' => 'صنعاء',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=85',
                'story' => 'مساحة للقهوة تُعنى بتقديم المشروبات الدافئة برسومات رغوة تُحضّر وقت الطلب.',
            ],
            [
                'slug' => 'fidda-wa-naqsh',
                'name' => 'فضة ونقش',
                'craft' => 'فضيات وحلي',
                'city' => 'يافع',
                'image' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=1000&q=85',
                'story' => 'قطع حلي بتفاصيل فضية وأحجار ملونة، مع عناية بشكل القطعة وتناسقها.',
            ],
            [
                'slug' => 'souq-tihama',
                'name' => 'سوق تهامة',
                'craft' => 'منتجات موسمية',
                'city' => 'تهامة',
                'image' => 'https://images.unsplash.com/photo-1630081015918-8a21078e5cee?auto=format&fit=crop&w=1000&q=85',
                'story' => 'سوق محلي يجمع منتجات موسمية من مزارعي تهامة، وتُعرض الثمار وفق ما يتوفر في الموسم.',
            ],
        ];
    }

    public function findArtisan(string $slug): ?array
    {
        foreach ($this->artisans() as $artisan) {
            if ($artisan['slug'] === $slug) {
                return $artisan;
            }
        }

        return null;
    }
}
