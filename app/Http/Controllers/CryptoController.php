<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CryptoController extends Controller
{
    // Halaman utama - list cryptocurrency
    public function index()
    {
        return view('crypto.index');
    }

    // Halaman chart untuk coin tertentu
    public function show($id)
    {
        try {
            $response = Http::timeout(5)->get("https://api.coingecko.com/api/v3/coins/{$id}", [
                'localization' => 'false',
                'tickers' => 'false',
                'market_data' => 'true',
                'community_data' => 'false',
                'developer_data' => 'false',
                'sparkline' => 'true'
            ]);

            if ($response->failed()) {
                abort(404, 'Coin not found');
            }

            $coin = $response->json();
            $sparkline = $coin['market_data']['sparkline_7d']['price'] ?? [];

            // Labels sparkline setiap jam, start 7 hari lepas
            $labels = [];
            if (!empty($sparkline)) {
                $totalPoints = count($sparkline);
                $interval = $totalPoints > 0 ? floor($totalPoints / 7) : 1;
                $startTime = now()->subDays(7)->timestamp;

                for ($i = 0; $i < $totalPoints; $i += $interval) {
                    $labels[] = Carbon::createFromTimestamp($startTime + $i * 3600)->format('H:i');
                }
            }

            return view('crypto.show', compact('coin', 'sparkline', 'labels'));

        } catch (\Exception $e) {
            return view('crypto.show')->withErrors(['msg' => 'Failed to fetch coin data: '.$e->getMessage()]);
        }
    }

    // API proxy untuk fetch top 30 coins (elak CORS, ada cache + fallback)
    public function apiCoins()
    {
        try {
            $coins = Cache::remember('top_coins', 300, function() {
                $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/coins/markets', [
                    'vs_currency' => 'usd',
                    'order' => 'market_cap_desc',
                    'per_page' => 30,
                    'page' => 1,
                    'sparkline' => false
                ]);

                if ($response->failed()) {
                    throw new \Exception('CoinGecko API failed');
                }

                return $response->json();
            });

            return response()->json($coins);

        } catch (\Exception $e) {
            \Log::error('API Coins error', ['message' => $e->getMessage()]);

            // Fallback: hanya Bitcoin
            return response()->json([
                [
                    'id' => 'bitcoin',
                    'symbol' => 'btc',
                    'name' => 'Bitcoin',
                    'image' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
                    'current_price' => 30000,
                    'market_cap' => 600000000000,
                    'market_cap_rank' => 1,
                    'price_change_percentage_24h' => 2.5,
                    'total_volume' => 35000000000
                ]
            ]);
        }
    }
}
