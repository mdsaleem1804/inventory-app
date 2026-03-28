<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #0f172a; margin: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .title { margin: 0; font-size: 22px; font-weight: 700; }
        .meta { margin: 0; font-size: 12px; color: #475569; }
        .filters { margin-bottom: 16px; font-size: 12px; }
        .filters strong { margin-right: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 700; }
        .footer { margin-top: 16px; color: #64748b; font-size: 11px; }
        @media print {
            body { margin: 8mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $title ?? 'Report' }}</h1>
        <p class="meta">Generated: {{ $generated_at ?? now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if(!empty($filters))
        <div class="filters">
            <strong>Filters:</strong>
            @foreach($filters as $label => $value)
                <span>{{ $label }}: {{ $value }}</span>@if(! $loop->last) | @endif
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach(($columns ?? []) as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse(($rows ?? []) as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns ?? []) }}">No data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Inventory Reporting Framework</p>

    @if(!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
