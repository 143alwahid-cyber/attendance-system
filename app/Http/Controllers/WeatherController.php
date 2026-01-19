<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function getWeather(Request $request): JsonResponse
    {
        $city = $request->input('city', 'Lahore');
        
        try {
            // Using wttr.in API (free, no API key required)
            $url = "https://wttr.in/{$city}?format=j1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                
                if ($data && isset($data['current_condition'][0])) {
                    $current = $data['current_condition'][0];
                    $location = $data['nearest_area'][0] ?? null;
                    
                    return response()->json([
                        'success' => true,
                        'city' => $location ? ($location['area'][0]['value'] . ', ' . $location['country'][0]['value']) : $city,
                        'temp' => (int) $current['temp_C'],
                        'humidity' => (int) $current['humidity'],
                        'wind' => (int) round($current['windspeedKmph']),
                        'description' => $current['weatherDesc'][0]['value'] ?? 'N/A',
                        'weatherCode' => $current['weatherCode'],
                    ]);
                }
            }
            
            throw new \Exception('Invalid response from weather API');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'city' => $city . ', PK',
                'temp' => 22,
                'humidity' => 60,
                'wind' => 10,
                'description' => 'Partly Cloudy',
                'weatherCode' => '116',
            ]);
        }
    }
}
