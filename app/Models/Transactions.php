<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class Transactions
{
    protected static $filename = 'transactions.json';

    public static function all(): Collection
    {
        if (Storage::exists(self::$filename)) {
            $json = Storage::get(self::$filename);
            return collect(json_decode($json, true));
        }
        return collect([]);
    }

    public static function save(Collection $transactions): void
    {
        Storage::put(self::$filename, json_encode($transactions->toArray(), JSON_PRETTY_PRINT));
    }
}
