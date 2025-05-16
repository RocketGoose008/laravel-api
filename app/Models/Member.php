<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    protected $table = 'members'; 

    protected $primaryKey = 'id';

    public $incrementing = false; 

    protected $keyType = 'string'; 

    public $timestamps = false; 

    protected $fillable = [
        'id',
        'personal_id',
        'member_type',
        'firstname',
        'lastname',
        'username',
        'email',
        'phone_number',
        'password',
        'create_date',
        'update_date',
        'language',
        'accept_consent',
    ];

    // Insert
    public static function allMembers()
    {
        if (Storage::exists('members.json')) {
            $json = Storage::get('members.json');
            return collect(json_decode($json, true));
        }
        return collect([]);
    }

    public static function saveMembers($members)
    {
        Storage::put('members.json', json_encode($members, JSON_PRETTY_PRINT));
    }
}
