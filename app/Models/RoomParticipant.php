<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RoomParticipant extends Model
{
    protected $fillable = [
        'room_id', 'student_id', 'score',
        'total_points', 'is_finished', 'joined_at', 'finished_at',
        'left_app_count', 'app_state', 'last_left_at'
    ];

    protected $casts = [
        'is_finished' => 'boolean',
        'joined_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_left_at' => 'datetime',
    ];

    public function room() { return $this->belongsTo(Room::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function essayAnswers() { return $this->hasMany(EssayAnswer::class); }
}