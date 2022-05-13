<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class miqNoteRelation extends Model
{
    use HasFactory;

    public function MiskitoWord()
    {
      return $this->hasMany('MiskitoWord');
    }
    public function Example()
    {
      return $this->hasMany('Example');
    }
}
