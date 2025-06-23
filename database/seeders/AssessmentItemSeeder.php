<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentItem;

class AssessmentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // المحور الأول: التحسن المرتبط بالأدوية النفسية (13 بنداً)
            ['axis_type' => 'medication', 'item_text_ar' => 'انتظام تناول الدواء بدون رفض أو مقاومة', 'criteria_1_ar' => 'يرفض بشدة', 'criteria_5_ar' => 'يطلب الدواء بنفسه'],
            ['axis_type' => 'medication', 'item_text_ar' => 'جودة النوم', 'criteria_1_ar' => 'لا ينام إطلاقًا', 'criteria_5_ar' => 'نوم منتظم 6-8 ساعات'],
            ['axis_type' => 'medication', 'item_text_ar' => 'انخفاض الهلاوس السمعية أو البصرية', 'criteria_1_ar' => 'يومية ومستمرة', 'criteria_5_ar' => 'اختفت أو نادرة جدًا'],
            ['axis_type' => 'medication', 'item_text_ar' => 'انخفاض الأفكار الضلالية', 'criteria_1_ar' => 'ثابتة ومسيطرة', 'criteria_5_ar' => 'لا توجد أو أصبح يرفضها'],
            ['axis_type' => 'medication', 'item_text_ar' => 'استبصار المريض بحالته', 'criteria_1_ar' => 'ينكر المرض تمامًا', 'criteria_5_ar' => 'يطلب العلاج ويصف حالته'],
            ['axis_type' => 'medication', 'item_text_ar' => 'انخفاض السلوك العدواني', 'criteria_1_ar' => 'عدواني دائم', 'criteria_5_ar' => 'لم يُسجَّل عليه أي عدوان'],
            ['axis_type' => 'medication', 'item_text_ar' => 'انخفاض الاستثارة والانفعال', 'criteria_1_ar' => 'شديد الاستثارة', 'criteria_5_ar' => 'هادئ ومُتحكِّم في انفعالاته'],
            ['axis_type' => 'medication', 'item_text_ar' => 'ثبات المزاج واستقراره', 'criteria_1_ar' => 'مُتقلِّب بشكل حاد', 'criteria_5_ar' => 'مستقر أغلب الوقت'],
            ['axis_type' => 'medication', 'item_text_ar' => 'انخفاض السلوكيات الغريبة', 'criteria_1_ar' => 'يومية ومتكررة', 'criteria_5_ar' => 'لم تعد موجودة'],
            ['axis_type' => 'medication', 'item_text_ar' => 'تحسن العناية الشخصية والاهتمام بالمظهر', 'criteria_1_ar' => 'مُهمِل تمامًا لنظافته', 'criteria_5_ar' => 'يهتم بنفسه ومظهره جيدًا'],
            ['axis_type' => 'medication', 'item_text_ar' => 'وجود أفكار انتحارية', 'criteria_1_ar' => 'أفكار شديدة ومستمرة', 'criteria_5_ar' => 'لا توجد أي أفكار انتحارية'],
            ['axis_type' => 'medication', 'item_text_ar' => 'استقرار الحركة', 'criteria_1_ar' => 'حركة كثيرة وغير هادفة', 'criteria_5_ar' => 'حركة طبيعية ومستقرة'],
            ['axis_type' => 'medication', 'item_text_ar' => 'الكلام', 'criteria_1_ar' => 'كلام كثير وغير مُترابِط', 'criteria_5_ar' => 'كلام طبيعي ومترابط'],

            // المحور الثاني: التحسن الناتج عن التدخلات النفسية (10 بنود)
            ['axis_type' => 'psychological', 'item_text_ar' => 'التواصل البصري أثناء الحديث', 'criteria_1_ar' => 'يتهرب من النظر تمامًا', 'criteria_5_ar' => 'تواصل بصري مباشر ومتزن'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'التعبير عن المشاعر بدقة ووضوح', 'criteria_1_ar' => 'لا يعبر إطلاقًا', 'criteria_5_ar' => 'يعبر عن مشاعره بوضوح وثقة'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'التفاعل الإيجابي مع الفريق النفسي', 'criteria_1_ar' => 'رفض تام وعزلة', 'criteria_5_ar' => 'تجاوب وحماس دائم'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'تقبل النقد أو التصحيح', 'criteria_1_ar' => 'يثور ويرفض بشدة', 'criteria_5_ar' => 'يتقبل ويناقش بهدوء'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'المشاركة في الجلسات الجماعية', 'criteria_1_ar' => 'لا يحضر أو ينسحب', 'criteria_5_ar' => 'يشارك بفعالية ويتفاعل'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'بناء علاقات مع زملائه المرضى', 'criteria_1_ar' => 'انعزال تام وعدائية', 'criteria_5_ar' => 'علاقات اجتماعية متعددة وإيجابية'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'التحكم في الانفعالات داخل الجلسة', 'criteria_1_ar' => 'صراخ أو خروج غاضب', 'criteria_5_ar' => 'هادئ ومسيطر على انفعالاته'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'الحديث عن الذات بإيجابية', 'criteria_1_ar' => 'نظرة سلبية مدمرة للذات', 'criteria_5_ar' => 'نظرة متزنة وإيجابية للذات'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'إظهار الرغبة في التعافي والتغيير', 'criteria_1_ar' => 'يرفض فكرة التحسن', 'criteria_5_ar' => 'يصر ويبحث عن طرق للتحسن'],
            ['axis_type' => 'psychological', 'item_text_ar' => 'حل المشكلات البسيطة أثناء الجلسة', 'criteria_1_ar' => 'عاجز تمامًا', 'criteria_5_ar' => 'يستخدم المهارات المكتسبة بفعالية'],

            // المحور الثالث: التحسن الناتج عن الأنشطة الرياضية والترفيهية (10 بنود)
            ['axis_type' => 'activities', 'item_text_ar' => 'الالتزام بالحضور في النشاط', 'criteria_1_ar' => 'لا يحضر إطلاقًا', 'criteria_5_ar' => 'منتظم ويحضر دائمًا'],
            ['axis_type' => 'activities', 'item_text_ar' => 'حماسه أثناء التمارين', 'criteria_1_ar' => 'سلبي تمامًا ومقاوم', 'criteria_5_ar' => 'مبادر ومتحمس جدًا'],
            ['axis_type' => 'activities', 'item_text_ar' => 'قدرته على اتباع التعليمات', 'criteria_1_ar' => 'يرفض اتباع التعليمات', 'criteria_5_ar' => 'ينفذ التعليمات بدقة وتركيز'],
            ['axis_type' => 'activities', 'item_text_ar' => 'تحمل التمارين دون انسحاب', 'criteria_1_ar' => 'ينسحب مباشرة', 'criteria_5_ar' => 'يواصل التمرين حتى النهاية'],
            ['axis_type' => 'activities', 'item_text_ar' => 'تفاعله الاجتماعي خلال النشاط', 'criteria_1_ar' => 'منعزل ولا يتحدث', 'criteria_5_ar' => 'نشيط ويتفاعل مع الجميع'],
            ['axis_type' => 'activities', 'item_text_ar' => 'تحسن المزاج بعد التمرين', 'criteria_1_ar' => 'يظل كئيبًا أو غاضبًا', 'criteria_5_ar' => 'يظهر عليه الفرح والانبساط'],
            ['axis_type' => 'activities', 'item_text_ar' => 'استقراره الحركي والانفعالي أثناء النشاط', 'criteria_1_ar' => 'مضطرب أو عدواني', 'criteria_5_ar' => 'متزن وهادئ'],
            ['axis_type' => 'activities', 'item_text_ar' => 'احترامه لقواعد النشاط والأنظمة', 'criteria_1_ar' => 'يخالف القواعد باستمرار', 'criteria_5_ar' => 'منضبط ويلتزم بالقواعد'],
            ['axis_type' => 'activities', 'item_text_ar' => 'طلبه المتكرر للمشاركة في النشاط', 'criteria_1_ar' => 'لا يطلب المشاركة أبدًا', 'criteria_5_ar' => 'يطلب المشاركة يوميًا'],
            ['axis_type' => 'activities', 'item_text_ar' => 'استخدامه للمهارات التي تعلمها في أنشطة أخرى', 'criteria_1_ar' => 'لا يستخدمها إطلاقًا', 'criteria_5_ar' => 'يُطبق ما تعلمه في حياته اليومية'],
        ];

        foreach ($items as $item) {
            // استخدام item_text_ar كمعرف فريد لمنع التكرار عند إعادة تشغيل الـ Seeder
            AssessmentItem::updateOrCreate(['item_text_ar' => $item['item_text_ar']], $item);
        }
    }
}