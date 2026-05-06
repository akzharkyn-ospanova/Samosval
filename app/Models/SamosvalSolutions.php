<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamosvalSolutions extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'title',
    ];

    protected $casts = [
        'problem_id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function problem()
    {
        return $this->belongsTo(SamosvalProblems::class, 'problem_id')->withDefault([
            'title' => 'Удаленная поломка',
        ]);
    }

    public function requests()
    {
        return $this->hasMany(SamosvalRequest::class, 'solution_id');
    }
}
