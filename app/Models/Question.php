<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'quiz_id', 'question', 'type', 'points',
        'order', 'image', 'explanation'
    ];

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function options() { return $this->hasMany(QuestionOption::class)->orderBy('order'); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function essayAnswers() { return $this->hasMany(EssayAnswer::class); }
}