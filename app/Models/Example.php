<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class example extends Model
{
    use HasFactory;

    //fillabeにしないと登録の際にエラー
    protected $fillable = ['miskito_sentence']; 

    public function miqExRelation(){
        return $this->belongTo('miqExRelation');
    }
}
