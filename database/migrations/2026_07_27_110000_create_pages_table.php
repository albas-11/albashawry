<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // about | policy | terms
            $table->json('title');
            $table->json('content');
            $table->boolean('is_active')->default(true);

            // SEO (mêmes champs que les produits)
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('seo_noindex')->default(false);

            $table->timestamps();
        });

        $now = now();

        DB::table('pages')->insert(array_map(fn($page) => $page + [
            'is_active' => true,
            'seo_noindex' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ], [
            [
                'slug' => 'about',
                'title' => json_encode(['en' => 'About Us', 'ar' => 'من نحن'], JSON_UNESCAPED_UNICODE),
                'content' => json_encode([
                    'en' => $this->aboutEn(),
                    'ar' => $this->aboutAr(),
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'slug' => 'policy',
                'title' => json_encode([
                    'en' => 'Policy of Refund and Exchange',
                    'ar' => 'سياسة الاسترجاع والتبديل',
                ], JSON_UNESCAPED_UNICODE),
                'content' => json_encode([
                    'en' => $this->policyEn(),
                    'ar' => $this->policyAr(),
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'slug' => 'terms',
                'title' => json_encode([
                    'en' => 'Terms of Use',
                    'ar' => 'شروط الاستخدام',
                ], JSON_UNESCAPED_UNICODE),
                'content' => json_encode([
                    'en' => $this->termsEn(),
                    'ar' => $this->termsAr(),
                ], JSON_UNESCAPED_UNICODE),
            ],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }

    /* ============ Contenu initial (repris des vues existantes) ============ */

    private function aboutEn(): string
    {
        return <<<'HTML'
<p>Al Bashawari Optical is known as the first and oldest optical name in the Kingdom, the name is known from our grandparents and continues to 3 generations for more than 100 years, we expanded in all regions of the Kingdom but specially we are the Eastern team, we started our first branch in Dammam until we reached Jazan recentally we have more than 13 branches, we offer you exclusive brands in the Kingdom such as Mango, Lamborghini, Pull &amp; Bear, Fisher Price, Barbie and more, our goal is to give you a pleasent shopping online experience and deliver you our products to all parts and cities of the Kingdom.</p>
<h2>100+ Years of Experience</h2>
<h2>13+ Branches</h2>
<h2>3 Generations</h2>
HTML;
    }

    private function aboutAr(): string
    {
        return <<<'HTML'
<p>تُعرف نظارات البشاوري بأنها أول وأقدم اسم في مجال البصريات في المملكة، اسم توارثناه عن أجدادنا واستمر لثلاثة أجيال ولأكثر من 100 عام. توسعنا في جميع مناطق المملكة، ونحن بالأخص فريق المنطقة الشرقية، بدأنا فرعنا الأول في الدمام حتى وصلنا إلى جازان، ولدينا اليوم أكثر من 13 فرعاً. نقدم لكم ماركات حصرية في المملكة مثل Mango وLamborghini وPull &amp; Bear وFisher Price وBarbie وغيرها. هدفنا أن نمنحكم تجربة تسوق إلكترونية ممتعة وأن نوصل منتجاتنا إلى جميع مناطق ومدن المملكة.</p>
<h2>أكثر من 100 عام من الخبرة</h2>
<h2>أكثر من 13 فرعاً</h2>
<h2>3 أجيال</h2>
HTML;
    }

    private function policyEn(): string
    {
        return <<<'HTML'
<h2>General Restrictions</h2>
<ul>
<li><strong>Contact Lenses:</strong> No refund or exchange.</li>
<li><strong>Medical Eyeglasses:</strong> No refund or exchange.</li>
<li><strong>Eyeglasses &amp; Sunglasses:</strong> No refund or exchange, <strong>except</strong> in the case of factory issues.</li>
</ul>
<h2>Exchange Period (Manufacturing Defects Only)</h2>
<p>You can replace Sunglasses within 7 days from the date of purchase, provided that:</p>
<ul>
<li>The glasses are in their original condition.</li>
<li>They are returned in their original box.</li>
</ul>
<p><strong>Shipping Costs:</strong> The customer will not be charged for shipping if a manufacturing defect is proven. You must send a picture of the product within 3 days of receipt.</p>
<h2>How to Request a Replacement</h2>
<p>To submit a request, please fill out the information in the Return Section at the bottom of this page within a maximum of one week.</p>
<h2>3-Month Warranty</h2>
<p>We provide a 3-month warranty from the date of purchase. This includes:</p>
<ul>
<li>One-time free maintenance for manufacturing and technical defects.</li>
<li><em>Note: This warranty does not cover damage caused by misuse.</em></li>
</ul>
<p>Thank you for choosing our products. For further assistance, please contact our support team.</p>
HTML;
    }

    private function policyAr(): string
    {
        return <<<'HTML'
<h2>القيود العامة</h2>
<ul>
<li><strong>عدسات لاصقة:</strong> لا استرجاع ولا تبديل.</li>
<li><strong>نظارات طبية:</strong> لا استرجاع ولا تبديل.</li>
<li><strong>نظارات شمسية وطبية:</strong> لا استرجاع ولا تبديل، <strong>إلا</strong> في حالة وجود مشاكل من المصنع.</li>
</ul>
<h2>فترة التبديل (لعيوب التصنيع فقط)</h2>
<p>يمكنك تبديل النظارات الشمسية خلال 7 أيام من تاريخ الشراء، بشرط أن:</p>
<ul>
<li>تكون النظارات في حالتها الأصلية.</li>
<li>يتم إرجاعها في صندوقها الأصلي.</li>
</ul>
<p><strong>تكاليف الشحن:</strong> لن يتم تحصيل رسوم الشحن من العميل إذا ثبت وجود عيب في التصنيع. يجب إرسال صورة للمنتج خلال 3 أيام من الاستلام.</p>
<h2>كيفية تقديم طلب التبديل</h2>
<p>لتقديم طلب التبديل، يرجى تعبئة المعلومات في قسم الاسترجاع في أسفل هذه الصفحة خلال مدة أقصاها أسبوع.</p>
<h2>ضمان لمدة 3 أشهر</h2>
<p>نحن نقدم ضمانًا لمدة 3 أشهر من تاريخ الشراء. ويشمل ذلك:</p>
<ul>
<li>صيانة مجانية لمرة واحدة للعيوب الفنية والتصنيعية.</li>
<li><em>ملاحظة: هذا الضمان لا يغطي الأضرار الناتجة عن سوء الاستخدام.</em></li>
</ul>
<p>شكرًا لاختياركم منتجاتنا. لمزيد من المساعدة، يرجى التواصل مع فريق الدعم لدينا.</p>
HTML;
    }

    private function termsEn(): string
    {
        return <<<'HTML'
<h2>1. Acceptance of the Terms</h2>
<p>By browsing or purchasing from this website, you agree to these Terms of Use. Please do not use the website if you do not agree to them.</p>
<h2>2. Products and Prices</h2>
<p>All prices are displayed in Saudi Riyal (SAR) and include applicable taxes unless stated otherwise. We make every effort to display product colours and details accurately, but slight differences may appear depending on your screen.</p>
<p>We reserve the right to modify prices and product availability at any time before an order is confirmed.</p>
<h2>3. Orders</h2>
<p>An order is confirmed only after payment has been received and validated. We reserve the right to refuse or cancel any order in the event of a pricing error, suspected fraud, or stock unavailability. In such a case, any amount already paid is refunded in full.</p>
<h2>4. Prescription Eyewear</h2>
<p>For any prescription product, the customer is responsible for the accuracy of the prescription data provided. We recommend relying on a valid prescription issued by an optician or an ophthalmologist.</p>
<h2>5. Delivery</h2>
<p>Delivery times are provided as an estimate and may vary depending on the destination city and the carrier. We cannot be held responsible for delays caused by the carrier or by circumstances beyond our control.</p>
<h2>6. Refund and Exchange</h2>
<p>Refunds and exchanges are governed by our Policy of Refund and Exchange, which forms an integral part of these Terms.</p>
<h2>7. Intellectual Property</h2>
<p>All content on this website — texts, logos, images and trademarks — is protected and may not be reproduced without prior written permission.</p>
<h2>8. Personal Data</h2>
<p>The data collected is used solely to process orders and to improve the customer experience. It is never sold to third parties.</p>
<h2>9. Changes to the Terms</h2>
<p>We may update these Terms at any time. The version published on this page is the one in force.</p>
<h2>10. Contact</h2>
<p>For any question regarding these Terms, please contact our customer support team.</p>
HTML;
    }

    private function termsAr(): string
    {
        return <<<'HTML'
<h2>1. قبول الشروط</h2>
<p>باستخدامك لهذا الموقع أو الشراء منه، فإنك توافق على شروط الاستخدام هذه. يُرجى عدم استخدام الموقع في حال عدم موافقتك عليها.</p>
<h2>2. المنتجات والأسعار</h2>
<p>جميع الأسعار معروضة بالريال السعودي (SAR) وتشمل الضرائب المطبقة ما لم يُذكر خلاف ذلك. نبذل قصارى جهدنا لعرض ألوان المنتجات وتفاصيلها بدقة، وقد تظهر اختلافات طفيفة حسب شاشة العرض.</p>
<p>نحتفظ بالحق في تعديل الأسعار وتوافر المنتجات في أي وقت قبل تأكيد الطلب.</p>
<h2>3. الطلبات</h2>
<p>لا يُعد الطلب مؤكداً إلا بعد استلام المبلغ والتحقق منه. ونحتفظ بالحق في رفض أو إلغاء أي طلب في حال وجود خطأ في السعر أو الاشتباه في عملية احتيال أو نفاد المخزون، ويُعاد في هذه الحالة أي مبلغ مدفوع بالكامل.</p>
<h2>4. النظارات الطبية</h2>
<p>بالنسبة لأي منتج طبي، يتحمل العميل مسؤولية صحة بيانات الوصفة الطبية المُدخلة. وننصح بالاعتماد على وصفة سارية صادرة عن أخصائي بصريات أو طبيب عيون.</p>
<h2>5. التوصيل</h2>
<p>مدد التوصيل تقديرية وقد تختلف حسب مدينة الوجهة وشركة الشحن. ولا نتحمل المسؤولية عن التأخير الناتج عن شركة الشحن أو عن ظروف خارجة عن إرادتنا.</p>
<h2>6. الاسترجاع والتبديل</h2>
<p>تخضع عمليات الاسترجاع والتبديل لسياسة الاسترجاع والتبديل الخاصة بنا، والتي تُعد جزءاً لا يتجزأ من هذه الشروط.</p>
<h2>7. الملكية الفكرية</h2>
<p>جميع محتويات هذا الموقع من نصوص وشعارات وصور وعلامات تجارية محمية ولا يجوز إعادة إنتاجها دون إذن كتابي مسبق.</p>
<h2>8. البيانات الشخصية</h2>
<p>تُستخدم البيانات التي نجمعها لمعالجة الطلبات وتحسين تجربة العميل فقط، ولا تُباع لأي طرف ثالث.</p>
<h2>9. تعديل الشروط</h2>
<p>يجوز لنا تحديث هذه الشروط في أي وقت، والنسخة المنشورة على هذه الصفحة هي النسخة السارية.</p>
<h2>10. التواصل</h2>
<p>لأي استفسار بخصوص هذه الشروط، يُرجى التواصل مع فريق خدمة العملاء لدينا.</p>
HTML;
    }
};
