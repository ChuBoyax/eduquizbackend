<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::where('teacher_id', $request->user()->teacher->id)
            ->withCount('questions')
            ->latest()
            ->get();

        return response()->json(['quizzes' => $quizzes]);
    }

    public function show(Request $request, $id)
    {
        $quiz = Quiz::where('teacher_id', $request->user()->teacher->id)
            ->with(['questions.options'])
            ->findOrFail($id);

        return response()->json(['quiz' => $quiz]);
    }
}