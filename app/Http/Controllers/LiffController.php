<?php

namespace App\Http\Controllers;

use App\Models\LineChannel;
use Illuminate\Http\Response;

class LiffController extends Controller
{
    public function __construct()
    {
    }

    public function show(string $endpointUuid)
    {
        $lineChannel = LineChannel::where('endpoint_uuid', $endpointUuid)->firstOrFail();

        $html = view('liff.show', [
            'liffId' => $lineChannel->liff_id,
        ])->render();

        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }
}