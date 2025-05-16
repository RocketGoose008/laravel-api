<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class SellOrders
{
    protected static $filename = 'sell_orders.json';

    public static function all()
    {
        if (Storage::exists(self::$filename)) {
            $json = Storage::get(self::$filename);
            return collect(json_decode($json, true));
        }
        return collect([]);
    }

    public static function save($orders)
    {
        Storage::put(self::$filename, json_encode($orders, JSON_PRETTY_PRINT));
    }
}
