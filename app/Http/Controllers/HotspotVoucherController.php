<?php

namespace App\Http\Controllers;

use App\Models\HotspotVoucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HotspotVoucherController extends Controller
{
    public function print($batchId, Request $request)
    {
        $vouchers = HotspotVoucher::with('profile')
            ->where('batch_id', $batchId)
            ->get();

        if ($vouchers->isEmpty()) {
            abort(404, 'Batch not found.');
        }

        $templateId = $request->query('template');
        $content = '';

        if ($templateId) {
            $template = \App\Models\HotspotVoucherTemplate::find($templateId);
            if ($template && $template->is_active) {
                // Render the custom template content
                $content = \Illuminate\Support\Facades\Blade::render($template->content, ['vouchers' => $vouchers]);
            }
        }

        // Fallback to default view if no template selected or template invalid
        if (empty($content)) {
            $content = view('hotspot.voucher-print', compact('vouchers'))->render();
        }

        return view('layouts.print-wrapper', ['slot' => $content]);
    }
}
