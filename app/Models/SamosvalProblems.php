<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamosvalProblems extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function solutions()
    {
        return $this->hasMany(SamosvalSolutions::class, 'problem_id');
    }

    public function requests()
    {
        return $this->hasMany(SamosvalRequest::class, 'problem_id');
    }
}
