<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StaffMember;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contacts',
        'comment',
        'source',
        'assigned_to',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function assignedStaffMember()
    {
        return $this->belongsTo(StaffMember::class, 'assigned_to');
    }
}
