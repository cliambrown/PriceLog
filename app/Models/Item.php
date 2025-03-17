<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

class Item extends Model
{
    // use HasFactory;
    
    protected function casts() {
        return [
            'id' => 'integer',
            'last_checked_at' => 'date',
        ];
    }
    
    protected $appends = ['filter_name'];
    
    protected function filterName(): Attribute {
        return Attribute::make(
            // get: fn (mixed $value, array $attributes) => Str::lower(Str::transliterate($attributes['name'])),
            get: fn (mixed $value, array $attributes) => simplify_string($attributes['name']),
        );
    }
    
    public function entries() {
        return $this->hasMany(Entry::class);
    }
}
