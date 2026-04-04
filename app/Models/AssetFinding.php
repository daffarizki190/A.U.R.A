<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetFinding extends Model
{
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

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
