<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardLink extends Model
{
    use HasFactory;
    protected $fillable = ['card_id','link','logo','title'];

    public function cardsuser()
    {
        return $this->belongsTo(Card::class,'card_id','id');
    }
}
