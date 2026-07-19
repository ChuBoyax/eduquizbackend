<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'school', 'subject', 'employee_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function quizzes() { return $this->hasMany(Quiz::class); }
    public function rooms() { return $this->hasMany(Room::class); }
}