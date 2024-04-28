<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondarySaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'type',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public static function getSecondarySavings()
    {
        return SecondarySaving::with('user:id', 'user.profile:user_id,name,member_id')->get();
    }
}
