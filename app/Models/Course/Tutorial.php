<?php

namespace App\Models\Course;

use App\Models\User\Student;
use App\Models\User\Teacher;
use Carbon;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $appends = [
        'start_time',
        'end_time',
        'human_date_time',
        'human_time',
    ];

    protected $with = ['timeSlot'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'course');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getStartTimeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date.' '.$this->start);
    }

    public function getEndTimeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date.' '.$this->timeSlot->getOriginal('end'));
    }

    public function getHumanDateTimeAttribute()
    {
        $timeSlot = $this->timeSlot;
        return humanDate($this->date, true).$timeSlot->day_part.' ' .$timeSlot->range;
    }

    public function getHumanTimeAttribute()
    {
        $timeSlot = $this->timeSlot;
        return humanDayOfWeek(Carbon::parse($this->date)->dayOfWeek).$timeSlot->day_part;
    }

    public function getNameAttribute()
    {
        return '1对1微信课程';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeOrderByLatest($query)
    {
        return $query->orderBy('date', 'desc')->orderBy('start', 'desc');
    }

    public function scopeOrderByEarliest($query)
    {
        return $query->orderBy('date', 'asc')->orderBy('start', 'asc');
    }

    public function scopeFollowingWeek($query)
    {
        return $this->scopeFollowingDays($query, 7);
    }

    public function scopeFollowingDays($query, $days)
    {
        return $query->where([
            ['date', '>=' , Carbon::now()->tomorrow()->toDateString()],
            ['date', '<', Carbon::now()->tomorrow()->addDays($days)->toDateString()]
        ]);
    }
}
