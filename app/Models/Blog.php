<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'blogs';
    protected $fillable = ['title', 'description'];


    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

public function likes()
{
    return $this->morphMany(Like::class, 'likeable');
}

}
