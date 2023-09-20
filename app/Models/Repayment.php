<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    protected $table = 'repayment';

    /**
  * The attributes that are mass assignable.
  *
  * @var array<int, string>
  */

 protected $fillable = [
     'repayment_ref_number',
     'loan_id',
     'loan_ref_number',
     'user_id',
     'repayment_amount',
     'repayment_date',
     'status',
 ];

 /**
  * The attributes that should be hidden for serialization.
  *
  * @var array<int, string>
  */
 protected $hidden = [
     'password',
     'remember_token',
 ];

 /**
  * The attributes that should be cast.
  *
  * @var array<string, string>
  */
 protected $casts = [
    'repayment_date' => 'date:Y-m-d',
    'updated_at' => 'datetime:Y-m-d H:i:s',
    'created_at' => 'datetime:Y-m-d H:i:s'
 ];
}
