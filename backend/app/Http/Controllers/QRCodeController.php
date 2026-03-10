<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    /**
     * Generate a QR code for a single table and return as base64 PNG.
     *
     * POST /api/qr/generate
     * Body: { "table": 5, "category": "Food" }
     */
    public function generate(Request $request)
    {
        $request->validate([
            'table'    => 'required|integer|min:1',
            'category' => 'nullable|string|max:50',
        ]);

        $url = $this->buildMenuUrl(
            $request->integer('table'),
            $request->string('category')
        );

        $qrPng = QrCode::format('png')
            ->size(300)
            ->errorCorrection('M')
            ->generate($url);

        return response()->json([
            'success'      => true,
            'table'        => $request->integer('table'),
            'menu_url'     => $url,
            'qr_base64'    => base64_encode($qrPng),
            'qr_data_url'  => 'data:image/png;base64,' . base64_encode($qrPng),
        ]);
    }

    /**
     * Bulk generate QR codes for a range of tables.
     * Returns a ZIP file with all PNGs.
     *
     * POST /api/qr/bulk
     * Body: { "from": 1, "to": 10 }
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'from' => 'required|integer|min:1',
            'to'   => 'required|integer|min:1|gte:from',
        ]);

        $from = $request->integer('from');
        $to   = $request->integer('to');

        $zip     = new \ZipArchive();
        $zipPath = storage_path("app/temp/qr-tables-{$from}-{$to}.zip");
        @mkdir(dirname($zipPath), 0755, true);

        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        for ($table = $from; $table <= $to; $table++) {
            $url   = $this->buildMenuUrl($table);
            $qrPng = QrCode::format('png')->size(300)->generate($url);
            $zip->addFromString("table-{$table}.png", $qrPng);
        }

        $zip->close();

        return response()->download($zipPath, "menu-qr-tables-{$from}-{$to}.zip")
            ->deleteFileAfterSend(true);
    }

    /**
     * Download QR for a single table as PNG.
     *
     * GET /api/qr/download/{table}
     */
    public function download(int $table)
    {
        $url   = $this->buildMenuUrl($table);
        $qrPng = QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($url);

        return response($qrPng)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"table-{$table}-qr.png\"");
    }

    // ── Helper ──────────────────────────────────────
    private function buildMenuUrl(int $table, ?string $category = null): string
    {
        $base = config('app.frontend_url', config('app.url')) . '/menu';
        $url  = "{$base}?table={$table}";
        if ($category) {
            $url .= '&category=' . urlencode($category);
        }
        return $url;
    }
}
