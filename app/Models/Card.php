<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'image',
        'qr_image',
        'title_color',
        'background_color',
        'icon_color',
        'share_color',
    ];

    public function user()
    {
    return $this->belongsTo(User::class,'user_id','id');
    }

    public function cardLinks()
    {
        return $this->hasMany(CardLink::class,'card_id','id');
    }

}
