<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'room_participant_id', 'question_id',
        'question_option_id', 'text_answer',
        'is_correct', 'points_earned'
    ];

    protected $casts = ['is_correct' => 'boolean'];

    public function participant() { return $this->belongsTo(RoomParticipant::class, 'room_participant_id'); }
    public function question() { return $this->belongsTo(Question::class); }
    public function option() { return $this->belongsTo(QuestionOption::class, 'question_option_id'); }
}