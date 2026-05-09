<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetFinding extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'finding_code',
        'finding_date',
        'location',
        'asset_type',
        'description',
        'photo',
        'reporter',
        'status',
        'pic_id',
        'estimated_completion_date',
        'actual_completion_date',
    ];

    protected $casts = [
        'finding_date' => 'date',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
    ];

    public function getFormattedFindingDateAttribute()
    {
        return $this->finding_date->format('d M Y');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Done' => 'badge-done',
            'On Progress' => 'badge-processed',
            'Pending' => 'badge-pendingapproval',
            default => 'badge-open',
        };
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
