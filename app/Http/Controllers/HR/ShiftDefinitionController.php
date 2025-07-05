<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ShiftDefinition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShiftDefinitionController extends Controller
{
    /**
     * عرض قائمة بكل تعريفات المناوبات.
     */
    public function index()
    {
        $shiftDefinitions = ShiftDefinition::orderBy('name')->paginate(10);
        return view('hr.shift_definitions.index', compact('shiftDefinitions'));
    }

    /**
     * عرض نموذج إنشاء تعريف مناوبة جديد.
     */
    public function create()
    {
        return view('hr.shift_definitions.create');
    }

    /**
     * تخزين تعريف مناوبة جديد في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        // قواعد التحقق الأساسية للحقول
        $rules = [
            'name' => 'required|string|max:100|unique:shift_definitions,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|numeric|min:0.5|max:48', // يمكن تعديل الحد الأقصى حسب الحاجة
            'color_code' => [
                'nullable',
                'string',
                'max:7',
                'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'
            ],
        ];

        // إنشاء مثيل Validator يدويًا لإضافة تحقق مخصص
        $validator = Validator::make($request->all(), $rules);

        // إضافة تحقق مخصص بعد القواعد الأساسية باستخدام خطاف "after"
        $validator->after(function ($validator) use ($request) {
            // نتأكد أولاً من وجود القيم لتجنب الأخطاء
            if ($request->filled('start_time') && $request->filled('end_time') && $request->filled('duration_hours')) {
                $startTime = $request->input('start_time');
                $endTime = $request->input('end_time');
                $duration = (float) $request->input('duration_hours');

                // حساب وقت الانتهاء المتوقع بناءً على وقت البدء والمدة
                $expectedEndTime = Carbon::parse($startTime)->addHours($duration)->format('H:i');

                // إذا كان وقت الانتهاء المتوقع لا يتطابق مع الوقت المدخل، أضف خطأ
                if ($expectedEndTime !== $endTime) {
                    $validator->errors()->add(
                        'end_time',
                        'وقت الانتهاء لا يتوافق مع وقت البدء والمدة. الوقت المتوقع هو: ' . $expectedEndTime
                    );
                }
            }
        });

        // تنفيذ التحقق وإعادة التوجيه في حالة الفشل
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ShiftDefinition::create($request->all());
        return redirect()->route('hr.shift_definitions.index')->with('success', 'تم إنشاء تعريف المناوبة بنجاح.');
    }


    /**
     * عرض نموذج تعديل تعريف مناوبة موجود.
     */
    public function edit(ShiftDefinition $shiftDefinition)
    {
        return view('hr.shift_definitions.edit', compact('shiftDefinition'));
    }

    /**
     * تحديث تعريف المناوبة في قاعدة البيانات.
     */
    public function update(Request $request, ShiftDefinition $shiftDefinition)
    {
        // قواعد التحقق الأساسية للحقول
        $rules = [
            'name' => ['required', 'string', 'max:100', Rule::unique('shift_definitions')->ignore($shiftDefinition->id)],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|numeric|min:0.5|max:48',
            'color_code' => [
                'nullable',
                'string',
                'max:7',
                'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'
            ],
        ];

        // إنشاء مثيل Validator يدويًا لإضافة تحقق مخصص
        $validator = Validator::make($request->all(), $rules);

        // إضافة تحقق مخصص بعد القواعد الأساسية باستخدام خطاف "after"
        $validator->after(function ($validator) use ($request) {
            if ($request->filled('start_time') && $request->filled('end_time') && $request->filled('duration_hours')) {
                $startTime = $request->input('start_time');
                $endTime = $request->input('end_time');
                $duration = (float) $request->input('duration_hours');

                $expectedEndTime = Carbon::parse($startTime)->addHours($duration)->format('H:i');

                if ($expectedEndTime !== $endTime) {
                    $validator->errors()->add(
                        'end_time',
                        'وقت الانتهاء لا يتوافق مع وقت البدء والمدة. الوقت المتوقع هو: ' . $expectedEndTime
                    );
                }
            }
        });

        // تنفيذ التحقق وإعادة التوجيه في حالة الفشل
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $shiftDefinition->update($request->all());
        return redirect()->route('hr.shift_definitions.index')->with('success', 'تم تحديث تعريف المناوبة بنجاح.');
    }


    /**
     * حذف تعريف مناوبة من قاعدة البيانات.
     */
    public function destroy(ShiftDefinition $shiftDefinition)
    {
        // منع الحذف إذا كان التعريف مستخدماً في جدول مناوبات الموظفين
        if ($shiftDefinition->employeeShifts()->count() > 0) {
            return redirect()->route('hr.shift_definitions.index')
                             ->with('error', 'لا يمكن الحذف. هذا التعريف مستخدم في جدول مناوبات الموظفين.');
        }

        $shiftDefinition->delete();
        return redirect()->route('hr.shift_definitions.index')->with('success', 'تم حذف تعريف المناوبة بنجاح.');
    }
}