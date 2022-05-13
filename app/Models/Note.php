<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;


    //fillabeにしないと登録の際にエラー
    
    protected $fillable = ['note']; 

    public function miqNoteRelation(){
        return $this->belongTo('miqNoteRelation');
    }
    
}
