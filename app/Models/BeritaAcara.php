<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
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

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
