<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use KKomelin\TranslatableStringExporter\Core\Utils\JSON;
use Monolog\Handler\IFTTTHandler;
use phpDocumentor\Reflection\Types\Array_;
use Throwable;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// ... other use statements

class Handler extends ExceptionHandler
{
    // ... other properties and methods

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // تحقق إذا كان الطلب لـ API (يعتمد على أن مساراتك تبدأ بـ api/ أو /v1/ كما في حالتك)
        // يمكنك استخدام 'api/*' إذا كانت مساراتك في routes/api.php
        if ($request->is('*/v1/*') || $request->wantsJson()) {

            // للتعامل مع أخطاء التحقق من البيانات (Validation) بشكل خاص
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            // للتعامل مع باقي الأخطاء
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $message = $e->getMessage();

            // لا تعرض تفاصيل الخطأ في بيئة الإنتاج
            if (config('app.debug') === false && $statusCode === 500) {
                $message = 'Server Error';
            }

            return response()->json([
                'message' => $message,
            ], $statusCode);
        }

        // إذا لم يكن طلب API، استمر في العرض بالطريقة الافتراضية (HTML)
        return parent::render($request, $e);
    }
}