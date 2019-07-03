<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sticky extends Model
{
   protected $fillable=[
       'title','description','user_id','is_done','date','color'
   ];

   public function user()
   {
       return $this->belongsTo('App\User', 'user_id');
   }
}
