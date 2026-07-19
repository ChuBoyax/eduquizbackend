<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $fillable = [
        'quiz_id', 'teacher_id', 'code',
        'status', 'is_battle', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'is_battle' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Leaderboard data na ginagamit ng API response at ng broadcast event
    public function buildLeaderboard(): array
    {
        return RoomParticipant::where('room_id', $this->id)
            ->with('student')
            ->orderByDesc('score')
            ->orderBy('finished_at')
            ->get()
            ->values()
            ->map(function ($p, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $p->student->name,
                    'score' => $p->score,
                    'total_points' => $p->total_points,
                    'percentage' => $p->total_points > 0
                        ? round(($p->score / $p->total_points) * 100)
                        : 0,
                    'is_finished' => $p->is_finished,
                    'finished_at' => $p->finished_at,
                    'left_app_count' => $p->left_app_count,
                    'app_state' => $p->app_state,
                    'last_left_at' => $p->last_left_at,
                ];
            })
            ->all();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($room) {
            // Auto-generate unique 6-digit code
            do {
                $code = strtoupper(Str::random(6));
            } while (self::where('code', $code)->exists());
            $room->code = $code;
        });
    }

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function participants() { return $this->hasMany(RoomParticipant::class); }
    public function students() { return $this->belongsToMany(Student::class, 'room_participants'); }
}