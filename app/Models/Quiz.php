<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'teacher_id', 'title', 'description', 'status',
        'time_limit', 'shuffle_questions', 'shuffle_options',
        'show_results_immediately'
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_results_immediately' => 'boolean',
    ];

    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('order'); }
    public function rooms() { return $this->hasMany(Room::class); }
}