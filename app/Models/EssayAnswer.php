<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EssayAnswer extends Model
{
    protected $fillable = [
        'room_participant_id', 'question_id',
        'answer', 'points_earned',
        'teacher_feedback', 'is_graded'
    ];

    protected $casts = ['is_graded' => 'boolean'];

    public function participant() { return $this->belongsTo(RoomParticipant::class, 'room_participant_id'); }
    public function question() { return $this->belongsTo(Question::class); }
}