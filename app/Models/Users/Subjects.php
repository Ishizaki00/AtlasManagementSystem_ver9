<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

use App\Models\Users\User;

class Subjects extends Model
{
    const UPDATED_AT = null;


    protected $fillable = [
        'subject'
    ];

    // public function users(){
    //     return $this->hasMany(User::class, 'subject_id');// リレーションの定義 追記：1つの科目に複数のユーザーが紐づくようにした
    // }
    public function users()
    {
        return $this->belongsToMany(User::class, 'subject_users', 'subject_id', 'user_id');
    }
}
