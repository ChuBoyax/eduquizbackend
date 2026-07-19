<?php
namespace App\Http\Controllers\Api;

use App\Events\LeaderboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Student;
use App\Models\RoomParticipant;
use App\Models\Quiz;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Teacher: gumawa ng room para sa quiz
    public function create(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'is_battle' => 'nullable|boolean',
        ]);

        $quiz = Quiz::where('teacher_id', $request->user()->teacher->id)
                    ->findOrFail($request->quiz_id);

        // I-close ang lahat ng existing rooms ng quiz na ito
        Room::where('quiz_id', $quiz->id)
            ->where('status', '!=', 'finished')
            ->update(['status' => 'finished']);

        // Gumawa ng bagong room (auto-generate ang 6-digit code sa model)
        $room = Room::create([
            'quiz_id' => $quiz->id,
            'teacher_id' => $request->user()->teacher->id,
            'status' => 'waiting',
            'is_battle' => $request->boolean('is_battle'),
        ]);

        return response()->json([
            'room' => [
                'id' => $room->id,
                'code' => $room->code,
                'status' => $room->status,
                'is_battle' => $room->is_battle,
                'quiz' => $quiz->only(['id', 'title', 'description']),
            ]
        ]);
    }

    // Student: mag-join gamit ang 6-digit code
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:100',
            'student_id' => 'nullable|string',
            'section' => 'nullable|string',
        ]);

        $room = Room::where('code', strtoupper($request->code))
                    ->whereIn('status', ['waiting', 'ongoing'])
                    ->with('quiz')
                    ->first();

        if (!$room) {
            return response()->json([
                'message' => 'Room not found or already finished.'
            ], 404);
        }

        // Gumawa ng student record
        $student = Student::create([
            'name' => $request->name,
            'student_id' => $request->student_id,
            'section' => $request->section,
        ]);

        // I-add sa room
        $participant = RoomParticipant::create([
            'room_id' => $room->id,
            'student_id' => $student->id,
            'joined_at' => now(),
        ]);

        // I-broadcast para makita agad ng teacher ang bagong sumali
        broadcast(new LeaderboardUpdated($room));

        return response()->json([
            'participant_id' => $participant->id,
            'student' => $student,
            'room' => [
                'id' => $room->id,
                'code' => $room->code,
                'status' => $room->status,
                'is_battle' => $room->is_battle,
                'quiz' => $room->quiz->only(['id', 'title', 'description', 'time_limit']),
            ]
        ]);
    }

    // Student: kunin ang mga tanong
    public function getQuestions($code)
    {
        $room = Room::where('code', strtoupper($code))
                    ->with(['quiz.questions.options'])
                    ->firstOrFail();

        if ($room->status === 'waiting') {
            return response()->json(['message' => 'Quiz has not started yet.'], 403);
        }

        $questions = $room->quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'points' => $question->points,
                'image' => $question->image,
                // Options lang para sa non-essay types
                'options' => $question->type !== 'essay'
                    ? $question->options->map(fn($o) => [
                        'id' => $o->id,
                        'option_text' => $o->option_text,
                    ])
                    : [],
            ];
        });

        return response()->json([
            'questions' => $questions,
            'time_limit' => $room->quiz->time_limit,
        ]);
    }

    // Teacher: i-start ang room
    public function start(Request $request, $code)
    {
        $room = Room::where('code', strtoupper($code))
                    ->where('teacher_id', $request->user()->teacher->id)
                    ->firstOrFail();

        $room->update([
            'status' => 'ongoing',
            'started_at' => now(),
        ]);

        return response()->json(['message' => 'Quiz started!', 'room' => $room]);
    }

    // Teacher: i-end ang room
    public function end(Request $request, $code)
    {
        $room = Room::where('code', strtoupper($code))
                    ->where('teacher_id', $request->user()->teacher->id)
                    ->firstOrFail();

        $room->update([
            'status' => 'finished',
            'ended_at' => now(),
        ]);

        return response()->json(['message' => 'Quiz ended!', 'room' => $room]);
    }

    // Student: i-report kapag umalis/bumalik sa app (anti-cheat)
    public function reportActivity(Request $request, $id)
    {
        $request->validate([
            'event' => 'required|in:left_app,resumed',
        ]);

        $participant = RoomParticipant::findOrFail($id);

        if ($request->event === 'left_app') {
            $participant->increment('left_app_count');
            $participant->update([
                'app_state' => 'left_app',
                'last_left_at' => now(),
            ]);
        } else {
            $participant->update(['app_state' => 'active']);
        }

        // Real-time cheat alert sa teacher screen
        broadcast(new LeaderboardUpdated($participant->room));

        return response()->json(['message' => 'Activity recorded.']);
    }

    // Teacher + Student: leaderboard
    public function leaderboard($code)
    {
        $room = Room::where('code', strtoupper($code))->firstOrFail();

        return response()->json(['leaderboard' => $room->buildLeaderboard()]);
    }

    // Teacher: detailed results
    public function results(Request $request, $code)
    {
        $room = Room::where('code', strtoupper($code))
                    ->where('teacher_id', $request->user()->teacher->id)
                    ->with(['participants.student', 'participants.answers', 'participants.essayAnswers'])
                    ->firstOrFail();

        return response()->json(['room' => $room]);
    }
}