<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function getExchangeRates()
    // {
    //     $response = Http::get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/'); // API ning manzilini o'rnating

    //     if ($response->ok()) {
    //         $exchangeRates = $response->json();
    //         // $exchangeRates = Currency::where("Ccy" , "USD")->get();
    //         // Valyuta kurslarini qaytarish yoki boshqa qismlarga o'tkazish
    //         // if ($exchangeRates['Ccy'] == "USD") {
    //             return  $exchangeRates;
    //         // }
            
    //     }
    //     // $exchangeRates = $response->json();
    //     // Currency::create($exchangeRates)->save(Currency::all());

    //     // Xatolik sodir bo'lganda xato xabarini qaytarish
    //     return response()->json(['error' => 'Failed to fetch exchange rates'], 500);
    // }
   
//     public function getFilteredExchangeRates(Request $request)
// {
//     $baseCurrency = $request->input('base_currency');
//     $targetCurrency = $request->input('target_currency');

//     $response = Http::get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/'); // API ning manzilini o'rnating

//     if ($response->ok()) {
//         $exchangeRates = $response->json();

//         // Filtrlash
//         if ($baseCurrency && $targetCurrency) {
//             $filteredRates = $exchangeRates[$baseCurrency][$targetCurrency];
//             return $filteredRates;
//         }

//         // Agar filtrlash parametrlari kiritilmagan bo'lsa, asosiy valyuta kurslari ro'yxatini qaytarish
//         return $exchangeRates;
//     }

//     // Xatolik sodir bo'lganda xato xabarini qaytarish
//     return response()->json(['error' => 'Failed to fetch exchange rates'], 500);
// }
    public function index()
    {
        $response = Http::get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/'); // API ning manzilini o'rnating

        if ($response->ok()) {
            $exchangeRates = $response->json();
            // return $exchangeRates;
            // Valyuta kurslarini qaytarish yoki boshqa qismlarga o'tkazish
            
            // return $exchangeRates;
            $usdRate = collect($exchangeRates)->first(function ($item) {
                return $item['Ccy'] === 'USD';
            });
            $eurRate = collect($exchangeRates)->first(function ($item) {
                return $item['Ccy'] === 'EUR';
            });
            $rubRate = collect($exchangeRates)->first(function ($item) {
                return $item['Ccy'] === 'RUB';
            });
            $gbpRate = collect($exchangeRates)->first(function ($item) {
                return $item['Ccy'] === 'GBP';
            });
            $aznRate = collect($exchangeRates)->first(function ($item) {
                return $item['Ccy'] === 'AZN';
            });
            
            if ($usdRate && $eurRate && $rubRate) {
                $result = [
                    'USD' => $usdRate['Rate'],
                    'EUR' => $eurRate['Rate'],
                    'RUB' => $rubRate['Rate'],
                    'GBP' => $gbpRate['Rate'],
                    'AZN' => $aznRate['Rate'],
                ];
        
                return response()->json($result);
            } else {
                return response()->json(['error' => 'Currency not found in the exchange rates.'], 404);
            }
                
        }
        
        

        // Xatolik sodir bo'lganda xato xabarini qaytarish
        return response()->json(['error' => 'Failed to fetch exchange rates'], 500);
    }
    public function show(Request $request,$id){
        // if(Currency::find($id)){
        //     return Currency::find($id);
        //      }else
        //      return response()->json(['message' => 'User not found'], 404);
             
    }
    /**
     * Show the form for creating a new resource.
     */
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        //
    }
}
