<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #1e293b; padding: 20px; line-height: 1.6; }
        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px; text-align: center; }
        .header h2 { margin: 0; color: #0f172a; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        .priority-block { margin-bottom: 30px; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; }
        .priority-header { padding: 10px 15px; font-weight: bold; font-size: 16px; color: white; display: flex; justify-content: space-between; }
        .CRITICAL .priority-header { background: #ef4444; }
        .HIGH .priority-header { background: #f97316; }
        .MEDIUM .priority-header { background: #eab308; }
        .LOW .priority-header { background: #22c55e; }
        .error-list { padding: 15px; }
        .error-item { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }
        .error-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .error-title { font-weight: 600; color: #334155; margin-bottom: 4px; display: block; }
        .error-meta { font-size: 12px; color: #94a3b8; font-family: monospace; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>📊 PulseAlert Daily Summary</h2>
            <p>Report for: {{ now()->subDay()->format('Y-m-d') }}</p>
        </div>

        @foreach($errors as $priority => $items)
        <div class="priority-block {{ $priority }}">
            <div class="priority-header">
                <span>{{ $priority }}</span>
                <span>{{ count($items) }} Errors</span>
            </div>
            <div class="error-list">
                @foreach($items as $log)
                <div class="error-item">
                    <span class="error-title">{{ class_basename($log->exception_class) }}: {{ $log->message }}</span>
                    <span class="error-meta">{{ $log->file }}:{{ $log->line }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="footer">
            &copy; {{ date('Y') }} PulseAlert Monitoring. All rights reserved.
        </div>
    </div>
</body>
</html>
