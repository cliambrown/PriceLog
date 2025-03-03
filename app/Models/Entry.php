<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Entry extends Model
{
    // use HasFactory;
    
    protected $touches = ['item'];
    
    protected $appends = ['seen_on_diff'];
    
    protected function casts() {
        return [
            'id' => 'integer',
            'is_sale' => 'boolean',
            'price' => 'decimal:2',
            'seen_on' => 'date',
        ];
    }
    
    public function item() {
        return $this->belongsTo(Item::class);
    }
    
    protected function seenOnDiff(): Attribute {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $dt = Carbon::parse($attributes['seen_on']);
                if ($dt->isCurrentDay()) return 'today';
                return $dt->diffForHumans();
            }
        );
    }
}
