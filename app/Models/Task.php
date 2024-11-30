<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    protected $appends = ['is_overdue'];
    protected $fillable = [
        'title', 'description', 'due_date',
        'status', 'deleted_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date < Carbon::now();
    }
}
