<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ba_number',
        'ba_type',
        'incident_date',
        'customer_name',
        'license_plate',
        'chronology',
        'status',
        'pic_id',
        'submitted_at',
        'approved_at',
        'attachment',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function getFormattedIncidentDateAttribute()
    {
        return $this->incident_date->format('d M Y');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Done' => 'badge-done',
            'Processed' => 'badge-processed',
            'Rejected' => 'badge-rejected',
            'Draft' => 'badge-draft',
            default => 'badge-pendingapproval',
        };
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
