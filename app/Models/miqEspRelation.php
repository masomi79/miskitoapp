<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class miqEspRelation extends Model
{
    use HasFactory;
    // 絶対に変更しないカラム
    protected $guarded = ['id'];
    public function MiskitoWord()
    {
      return $this->hasMany('MiskitoWord');
    }
    public function SpanishWord()
    {
      return $this->hasMany('SpanishWord');
    }
}
