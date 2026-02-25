<?php

namespace App\Http\Controllers;

use App\Models\UserFeedback;
use App\Models\UserSegment;
use App\Models\UserSession;
use Illuminate\Http\Request;

class ClubMemberTrackingController extends Controller
{
    // Store a new session record
    public function storeSession(Request $request, $clubMemberId)
    {
        $session = UserSession::create([
            'club_member_id' => $clubMemberId,
            'login_time' => now(),
            'session_end' => null,
            'session_duration' => null,
            'sections_visited' => $request->input('sections_visited', []),
        ]);

        return response()->json($session);
    }

    // Update session duration and end time
    public function endSession($sessionId)
    {
        $session = UserSession::findOrFail($sessionId);
        $session->session_end = now();
        $session->session_duration = $session->login_time->diffInSeconds($session->session_end);
        $session->save();

        return response()->json($session);
    }

    // Store feedback from a club member
    public function storeFeedback(Request $request)
    {
        $clubMemberId = session('club_member_id');

        if (! $clubMemberId) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'You need to be logged in to submit feedback.');
        }

        $request->validate([
            'feedback_text' => 'required|string',
        ]);

        UserFeedback::create([
            'club_member_id' => $clubMemberId,
            'feedback_text' => $request->feedback_text,
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    // Update user segment
    public function updateSegment(Request $request, $clubMemberId)
    {
        $segment = UserSegment::updateOrCreate(
            ['club_member_id' => $clubMemberId],
            ['segment_type' => $request->input('segment_type'), 'updated_at' => now()]
        );

        return response()->json($segment);
    }

    // Retrieve sessions, feedback, and segments for a club member
    public function getMemberData($clubMemberId)
    {
        $sessions = UserSession::where('club_member_id', $clubMemberId)->get();
        $feedback = UserFeedback::where('club_member_id', $clubMemberId)->get();
        $segment = UserSegment::where('club_member_id', $clubMemberId)->first();

        return response()->json([
            'sessions' => $sessions,
            'feedback' => $feedback,
            'segment' => $segment,
        ]);
    }
}
