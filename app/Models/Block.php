<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Block extends Model {
    protected $fillable = ['rundown_id', 'title', 'order_index'];
    
    public function segments() {
        return $this->hasMany(Segment::class)->orderBy('order_index');
    }
}