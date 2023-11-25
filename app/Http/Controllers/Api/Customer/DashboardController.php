<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index() {
        $pending = Invoice::where('status', 'pending')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $success = Invoice::where('status', 'success')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $expired = Invoice::where('status', 'expired')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $failed = Invoice::where('status', 'failed')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();

        // response
        return response()->json([
            'success' => true,
            'message' => 'Statistik Data',
            'data' => [
                'count' => [
                    'pending' => $pending,
                    'success' => $success,
                    'expired' => $expired,
                    'failed' => $failed,
                ],
            ]
        ], 200);
    }
}
