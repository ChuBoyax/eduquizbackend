<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'student_id', 'section'];

    public function participants() { return $this->hasMany(RoomParticipant::class); }
    public function rooms() { return $this->belongsToMany(Room::class, 'room_participants'); }
}