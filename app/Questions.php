<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    
	protected $table = "questions";	 
    
    protected $fillable = [
        'correct_answer','options','instruction_id','question'
    ];   

     public function instruction()
    {
        return $this->hasOne('App\Instructions','id','instruction_id');
    }

}
