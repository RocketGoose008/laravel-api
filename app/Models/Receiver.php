<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Receiver extends Model
{
    protected $table = 'receivers';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'account_no',
        'firstname',
        'lastname',
        'phone_number',
        'create_date',
        'update_date',
    ];

    public static function allReceivers()
    {
        if (Storage::exists('receivers.json')) {
            $json = Storage::get('receivers.json');
            return collect(json_decode($json, true));
        }
        return collect([]);
    }

    public static function saveReceivers($receivers)
    {
        Storage::put('receivers.json', json_encode($receivers, JSON_PRETTY_PRINT));
    }
}
