<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeLimitedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            $exam_id = $request->route('id');
           $exam_obj = Exam::find($exam_id);

           // Assuming your database stores times in UTC:
           $examStartTime = Carbon::parse($exam_obj->exam_day_start_point)->setTimezone('Asia/Damascus');
           $examEndTime = Carbon::parse($exam_obj->exam_day_end_point)->setTimezone('Asia/Damascus');

           // Get current time in Asia/Damascus
           $now = Carbon::now('Asia/Damascus');

           if ($now->lt($examStartTime)) {
               return response()->json(['message' => 'وقت الامتحان لم يبدا بعد'], 403);
           }

           if ($now->gt($examEndTime)) {
               return response()->json(['message' => 'وقت الامتحان قد انتهى '], 403);
           }

           return $next($request);

        // $exam_id = $request->route('id');
        // $exam_obj = Exam::find($exam_id);
        // $examStartTime = Carbon::parse($exam_obj->exam_day_start_point);
        // $examEndTime = Carbon::parse($exam_obj->exam_day_end_point);
        // $now = Carbon::now();

        // if ($now->lt($examStartTime)) {
        //     return response()->json(['message' => 'Exam has not started yet.'], 403);
        // }

        // if ($now->gt($examEndTime)) {
        //     return response()->json(['message' => 'Exam time is over.'], 403);
        // }

        // return $next($request);
    }
}
