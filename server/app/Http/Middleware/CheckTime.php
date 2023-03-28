<?php
/*
namespace App\Http\Middleware;

use App\Models\ExchangeRate;
use Closure;
use Carbon\Carbon;

class CheckTime
{
    public function handle($request, Closure $next)
    {
        $api = file_get_contents('https://api.genelpara.com/embed/doviz.json');
        $exchanges = json_decode($api, true);
        $exchanges = collect($exchanges)->only(['USD', 'EUR', 'GBP'])->toArray();

        if (!ExchangeRate::today()->exists()) {
            ExchangeRate::create([
                'date' => Carbon::now('Europe/Istanbul')->format('Y-m-d'),
                'rates' => $exchanges
            ]);
        }
        return $next($request);
    }
}
*/

namespace App\Http\Middleware;

use App\Models\ExchangeRate;
use Closure;
use Carbon\Carbon;

class CheckTime
{
    public function handle($request, Closure $next)
    {
        $api = file_get_contents('https://api.genelpara.com/embed/doviz.json');
        $exchanges = json_decode($api, true);
        $exchanges = collect($exchanges)->only(['USD', 'EUR', 'GBP'])->toArray();

        $exchangeToday = ExchangeRate::today();
        $now = Carbon::now('Europe/Istanbul')->format('H:i:s');

        if (!$exchangeToday->exists()) {
            ExchangeRate::create([
                'date' => Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s'),
                'rates' => $exchanges
            ]);
        } elseif ($now > '15:00:00') {
            if($exchangeToday->first()->date < '15:00:00') {
                $exchangeToday->update([
                    'date' => Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s'),
                    'rates' => $exchanges
                ]);
            }
        }

        return $next($request);
    }
}
