<?php
namespace App\Http\Controllers\Api;

use App\Events\LeaderboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\EssayAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\RoomParticipant;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    // Student: mag-submit ng sagot (lahat sabay-sabay — classic mode)
    public function submit(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:room_participants,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.type' => 'required|string',
            // Para sa multiple choice
            'answers.*.option_id' => 'nullable|exists:question_options,id',
            // Para sa fill in the blank at true/false
            'answers.*.text_answer' => 'nullable|string',
            // Para sa essay
            'answers.*.essay_answer' => 'nullable|string',
        ]);

        $participant = RoomParticipant::findOrFail($request->participant_id);
        $totalScore = 0;
        $totalPoints = 0;

        foreach ($request->answers as $answerData) {
            $result = $this->scoreAnswer($participant, $answerData);
            $totalScore += $result['points_earned'];
            $totalPoints += $result['points_possible'];
        }

        // I-update ang score ng participant
        $participant->update([
            'score' => $totalScore,
            'total_points' => $totalPoints,
            'is_finished' => true,
            'finished_at' => now(),
        ]);

        broadcast(new LeaderboardUpdated($participant->room));

        return response()->json([
            'message' => 'Answers submitted successfully!',
            'score' => $totalScore,
            'total_points' => $totalPoints,
            'percentage' => $totalPoints > 0
                ? round(($totalScore / $totalPoints) * 100)
                : 0,
        ]);
    }

    // Student: isang sagot lang (Live Battle Mode) — agad na-uupdate ang leaderboard
    public function submitOne(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:room_participants,id',
            'question_id' => 'required|exists:questions,id',
            'type' => 'required|string',
            'option_id' => 'nullable|exists:question_options,id',
            'text_answer' => 'nullable|string',
            'essay_answer' => 'nullable|string',
        ]);

        $participant = RoomParticipant::findOrFail($request->participant_id);

        $result = $this->scoreAnswer($participant, $request->all());

        $participant->increment('total_points', $result['points_possible']);
        if ($result['points_earned'] > 0) {
            $participant->increment('score', $result['points_earned']);
        }
        $participant->refresh();

        broadcast(new LeaderboardUpdated($participant->room));

        return response()->json([
            'message' => 'Answer recorded!',
            'score' => $participant->score,
            'total_points' => $participant->total_points,
        ]);
    }

    // Student: tapusin ang quiz (Live Battle Mode)
    public function finish(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:room_participants,id',
        ]);

        $participant = RoomParticipant::findOrFail($request->participant_id);
        $participant->update([
            'is_finished' => true,
            'finished_at' => now(),
        ]);

        broadcast(new LeaderboardUpdated($participant->room));

        return response()->json([
            'message' => 'Quiz finished!',
            'score' => $participant->score,
            'total_points' => $participant->total_points,
            'percentage' => $participant->total_points > 0
                ? round(($participant->score / $participant->total_points) * 100)
                : 0,
        ]);
    }

    // I-grade ang isang sagot at i-save; ibinabalik ang earned/possible points
    private function scoreAnswer(RoomParticipant $participant, array $answerData): array
    {
        $question = Question::with('options')->find($answerData['question_id']);
        $pointsEarned = 0;

        if ($question->type === 'essay') {
            // Essay — hindi agad nagre-grade, teacher ang mag-ge-grade
            EssayAnswer::create([
                'room_participant_id' => $participant->id,
                'question_id' => $question->id,
                'answer' => $answerData['essay_answer'] ?? '',
                'is_graded' => false,
            ]);
        } elseif ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $selectedOption = isset($answerData['option_id'])
                ? QuestionOption::find($answerData['option_id'])
                : null;
            $isCorrect = $selectedOption && $selectedOption->is_correct
                && $selectedOption->question_id === $question->id;
            $pointsEarned = $isCorrect ? $question->points : 0;

            Answer::create([
                'room_participant_id' => $participant->id,
                'question_id' => $question->id,
                'question_option_id' => $answerData['option_id'] ?? null,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);
        } elseif ($question->type === 'fill_in_the_blank') {
            // Simple exact match (case-insensitive)
            $correctOption = $question->options->firstWhere('is_correct', true);
            $isCorrect = $correctOption &&
                strtolower(trim($answerData['text_answer'] ?? '')) ===
                strtolower(trim($correctOption->option_text));
            $pointsEarned = $isCorrect ? $question->points : 0;

            Answer::create([
                'room_participant_id' => $participant->id,
                'question_id' => $question->id,
                'text_answer' => $answerData['text_answer'] ?? null,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);
        }

        return [
            'points_earned' => $pointsEarned,
            'points_possible' => $question->points,
        ];
    }

    // Teacher: i-grade ang essay answer
    public function gradeEssay(Request $request, $id)
    {
        $request->validate([
            'points_earned' => 'required|integer|min:0',
            'feedback' => 'nullable|string',
        ]);

        $essay = EssayAnswer::findOrFail($id);
        $essay->update([
            'points_earned' => $request->points_earned,
            'teacher_feedback' => $request->feedback,
            'is_graded' => true,
        ]);

        // I-update ang total score ng participant
        $participant = $essay->participant;
        $totalScore = $participant->answers->sum('points_earned')
            + EssayAnswer::where('room_participant_id', $participant->id)
                ->where('is_graded', true)
                ->sum('points_earned');

        $participant->update(['score' => $totalScore]);

        broadcast(new LeaderboardUpdated($participant->room));

        return response()->json([
            'message' => 'Essay graded successfully!',
            'essay' => $essay,
        ]);
    }
}
