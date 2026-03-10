<?php

namespace App\Http\Controllers\Admin;

use App\Models\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Return all tables as JSON.
     */
    public function index()
    {
        $tables = Table::with(['activeOrder.items.menuItem'])->get();
        
        // Convert to array to ensure all fields are included
        $tablesArray = $tables->map(function ($table) {
            $tableArray = $table->toArray();
            // Ensure active_order field exists
            if (!isset($tableArray['active_order'])) {
                $tableArray['active_order'] = null;
            }
            return $tableArray;
        });
        
        return response()->json($tablesArray);
    }

    /**
     * Get table by token.
     */
    public function showByToken($token)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        return response()->json($table);
    }

    /**
     * Create a new table.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|unique:restaurant_tables,number',
        ]);

        $table = Table::create([
            'number' => $request->number,
        ]);

        return response()->json($table, 201);
    }

    /**
     * Update a table's name (number).
     */
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'number' => 'required|string|unique:restaurant_tables,number,' . $table->id,
        ]);

        $table->update([
            'number' => $request->number,
        ]);

        return response()->json($table);
    }

    /**
     * Generate QR code for a single table.
     */
    public function generateQr($id)
    {
        $table = Table::findOrFail($id);

        // Generate unique token
        $token = Str::uuid();
        $table->qr_token = $token;

        // URL customers will open (pointing to the frontend)
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $url = "{$frontendUrl}/menu/{$token}";

        // Generate QR image as SVG
        $fileName = "qrcodes/table_{$table->id}.svg";
        $filePath = public_path($fileName);
        $directoryPath = dirname($filePath);

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        QrCode::format('svg')
            ->size(300)
            ->encoding('UTF-8')
            ->errorCorrection('H')
            ->margin(1)
            ->generate($url, $filePath);

        $table->qr_code_path = $fileName;
        $table->save();

        return response()->json([
            'message' => 'QR Code generated successfully',
            'qr_code_url'  => asset($fileName),
        ]);
    }

    /**
     * Return the stored QR URL for a table.
     */
    public function getQr($id)
    {
        $table = Table::findOrFail($id);

        return response()->json([
            'qr_code_url' => $table->qr_code_path
                ? asset($table->qr_code_path)
                : null,
        ]);
    }

    /**
     * Delete a table.
     */
    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        
        // Delete QR code file if it exists
        if ($table->qr_code_path) {
            $qrFilePath = public_path($table->qr_code_path);
            if (File::exists($qrFilePath)) {
                File::delete($qrFilePath);
            }
        }
        
        $table->delete();
        
        return response()->json(['message' => 'Table deleted successfully']);
    }

    /**
     * Generate QR codes for every table.
     */
    public function generateAllQRs()
    {
        $tables = Table::all();
        $results = [];

        foreach ($tables as $table) {
            $token = Str::uuid();
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $url = "{$frontendUrl}/menu/{$token}";
            $fileName = "qrcodes/table_{$table->id}.svg";
            $filePath = public_path($fileName);
            $dir      = dirname($filePath);

            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            QrCode::format('svg')
                ->size(300)
                ->encoding('UTF-8')
                ->errorCorrection('H')
                ->margin(1)
                ->generate($url, $filePath);

            $table->qr_token    = $token;
            $table->qr_code_path = $fileName;
            $table->save();

            $results[] = [
                'table_id' => $table->id,
                'number'   => $table->number,
                'qr_url'   => asset($fileName),
            ];
        }

        return response()->json([
            'message' => 'All QR codes generated',
            'tables'  => $results,
        ]);
    }
}
