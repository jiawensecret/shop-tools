<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    //
    protected $guarded = ['id'];

    protected $appends = ['person_name','account_name'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function getPersonNameAttribute($value)
    {
        return $this->person->name ?? '';
    }

    public function getAccountNameAttribute($value)
    {
        return $this->account->account ?? '';
    }
}
