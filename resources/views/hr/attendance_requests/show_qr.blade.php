<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code للموظف {{ $request->employee->full_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .qr-container { text-align: center; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h3 { margin-bottom: 20px; }
        #countdown { font-size: 1.2rem; color: #dc3545; margin-top: 15px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="qr-container">
        <h3>تحضير الموظف: {{ $request->employee->full_name }}</h3>
        <p>يرجى من الموظف مسح هذا الرمز خلال المدة المحددة.</p>
        <div class="visible-print text-center">
            {!! QrCode::size(250)->generate($urlToVerify); !!}
        </div>
        <div id="countdown"></div>
    </div>

    <script>
        var expiresAt = new Date("{{ $request->expires_at->toIso8601String() }}").getTime();
        var countdownElement = document.getElementById("countdown");

        var interval = setInterval(function() {
            var now = new Date().getTime();
            var distance = expiresAt - now;

            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = "ينتهي خلال: " + minutes + "د " + seconds + "ث ";

            if (distance < 0) {
                clearInterval(interval);
                countdownElement.innerHTML = "انتهت صلاحية الرمز";
                document.querySelector('.qr-container').style.opacity = '0.5';
            }
        }, 1000);
    </script>
</body>
</html>