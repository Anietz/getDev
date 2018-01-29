<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instructions extends Model
{
    
	protected $table = "instructions";	 
    
    protected $fillable = [
        'description'
    ];

     public function question()
    {
        return $this->belongsTo('App\Question','instruction_id','id');
    }

}
