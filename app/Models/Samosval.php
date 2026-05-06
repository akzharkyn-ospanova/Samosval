<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Samosval extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'system_id',
        'type',
        'address',
        'serial_number',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function requests()
    {
        return $this->hasMany(SamosvalRequest::class, 'samosval_id');
    }
}
