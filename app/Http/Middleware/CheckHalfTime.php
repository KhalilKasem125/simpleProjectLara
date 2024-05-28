<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHalfTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $examId = $request->route('id');
        $exam = Exam::findOrFail($examId);

        $now = Carbon::now('Asia/Damascus');

        // Calculate the midpoint of the exam duration
        $midpoint = $exam->exam_day_start_point->copy()->addMinutes($exam->exam_day_start_point->diffInMinutes($exam->exam_day_end_point) / 2);

        // Allow submission only after the midpoint
        if ($now->lt($midpoint) || $now->gt($exam->exam_day_end_point)) {
            return response()->json(['message' => 'ليس مم المسموح الانهاء في هذا الوقت'], 403);
        }

        return $next($request);
    }
}


