<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SamosvalRequest extends Model
{
    use HasFactory;

    const EXPIRES_IN_HOURS = 2;

    const STATUS_OPEN = 0;
    const STATUS_IN_WORK = 1;
    const STATUS_WAITING_PARTS = 2;
    const STATUS_COMPLETED = 3;

    protected $fillable = [
        'samosval_id',
        'problem_id',
        'status',
        'solution_id',
        'expires_at',
    ];

    protected $appends = [
        'status_label',
        'current_status',
    ];

    protected $casts = [
        'samosval_id' => 'integer',
        'problem_id' => 'integer',
        'status' => 'integer',
        'solution_id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'expires_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected static function booted()
    {
        static::creating(function ($request) {
            if (!$request->expires_at) {
                $request->expires_at = Carbon::now()->addHours(self::EXPIRES_IN_HOURS);
            }
        });
    }

    public function samosval() {
        return $this->belongsTo(Samosval::class, 'samosval_id');
    }

    public function problem() {
        return $this->belongsTo(SamosvalProblems::class, 'problem_id');
    }

    public function solution() {
        return $this->belongsTo(SamosvalSolutions::class, 'solution_id')->withDefault();
    }


    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_OPEN => 'Новая',
            self::STATUS_IN_WORK => 'В работе',
            self::STATUS_WAITING_PARTS => 'Ожидает запчасти',
            self::STATUS_COMPLETED => 'Завершена',
            default => 'Неизвестно',
        };
    }

    public function getCurrentStatusAttribute()
    {
        return $this->status;
    }
}
