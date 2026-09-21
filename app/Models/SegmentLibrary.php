<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentLibrary extends Model
{
    protected $table = 'segment_library';

    protected $fillable = [
        'title', 'type', 'duration_seconds',
        'has_script', 'script_content',
        'production_notes', 'created_by',
    ];

    protected $casts = ['has_script' => 'boolean'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}