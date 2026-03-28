<?php

namespace App\Services;

use App\Exports\ReportArrayExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View as ViewContract;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportService
{
    public function exportToPdf(array $data, string $view): Response
    {
        $filename = $data['filename'] ?? 'report.pdf';

        return Pdf::loadView($view, $data)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function exportToExcel(array $data, string $filename): BinaryFileResponse
    {
        return Excel::download(
            new ReportArrayExport($data['columns'] ?? [], $data['rows'] ?? []),
            $filename
        );
    }

    public function exportToPrint(string $view, array $data = []): ViewContract
    {
        $data['autoPrint'] = true;

        return view($view, $data);
    }
}
