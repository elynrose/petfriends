<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CreditLog;
use Illuminate\Support\Facades\Auth;

class CreditLogController extends Controller
{
    public function index()
    {
        $creditLogs = CreditLog::with(['booking', 'booking.pet'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalCredits = Auth::user()->credits;

        return view('frontend.credit_logs.index', compact('creditLogs', 'totalCredits'));
    }
} 