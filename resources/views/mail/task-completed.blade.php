
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;margin:0;padding:20px;color:#111827}
        .wrap{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08)}
        .hdr{background:#2563eb;padding:32px;text-align:center}
        .hdr h1{color:#fff;font-size:22px;margin:0 0 4px}
        .hdr p{color:#bfdbfe;font-size:14px;margin:0}
        .body{padding:28px 32px}
        .greeting{font-size:15px;color:#374151;margin-bottom:20px;line-height:1.6}
        .stats{display:flex;gap:12px;margin-bottom:20px}
        .stat{flex:1;background:#f0f9ff;border-radius:10px;padding:16px;text-align:center}
        .stat-val{font-size:28px;font-weight:800;color:#2563eb}
        .stat-lbl{font-size:12px;color:#6b7280;margin-top:4px}
        .points{background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px;text-align:center;margin-bottom:20px;font-size:15px;color:#16a34a;font-weight:600}
        .msg{font-size:14px;color:#6b7280;line-height:1.6;margin-bottom:24px}
        .cta{display:block;background:#2563eb;color:#fff;text-align:center;padding:14px 24px;border-radius:10px;text-decoration:none;font-size:15px;font-weight:600}
        .footer{text-align:center;padding:20px 32px;border-top:1px solid #f3f4f6;font-size:12px;color:#9ca3af}
    </style>
</head>
<body>
<div class="wrap">
    <div class="hdr">
        <h1>✅ All tasks completed!</h1>
        <p>TekioTask — Daily Routine Update</p>
    </div>
    <div class="body">
        <p class="greeting">
            Great news! <strong>{{ $studentName }}</strong> has completed
            all assigned tasks for today's <strong>{{ $routineName }}</strong>.
        </p>
        <div class="stats">
            <div class="stat">
                <div class="stat-val">{{ $tasksCompleted }}</div>
                <div class="stat-lbl">Tasks Done</div>
            </div>
            <div class="stat">
                <div class="stat-val">{{ $totalTasks }}</div>
                <div class="stat-lbl">Total Tasks</div>
            </div>
        </div>
        <div class="points">🏆 +{{ $pointsEarned }} points earned today!</div>
        <p class="msg">
            Log in to your parent dashboard to view the full progress history,
            add comments about home behaviour, or record any tasks completed at home.
        </p>
        <a href="{{ config('app.url') }}/parent/dashboard" class="cta">
            View Dashboard →
        </a>
    </div>
    <div class="footer">
        TekioTask · Smart Integrated Therapy Centre, Shah Alam<br>
        You received this because you are linked as a parent in the system.
    </div>
</div>
</body>
</html>
