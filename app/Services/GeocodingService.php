<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeocodingService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Get coordinates from address
     *
     * @param string $address
     * @return array|null
     */
    public function getCoordinatesFromAddress($address)
    {
        try {
            $response = Http::get($this->baseUrl, [
                'address' => $address,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    return [
                        'latitude' => $location['lat'],
                        'longitude' => $location['lng']
                    ];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get address from coordinates
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            $response = Http::get($this->baseUrl, [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $addressComponents = $data['results'][0]['address_components'];
                    $formattedAddress = $data['results'][0]['formatted_address'];
                    
                    $address = [
                        'formatted_address' => $formattedAddress,
                        'street_number' => null,
                        'route' => null,
                        'locality' => null,
                        'administrative_area' => null,
                        'postal_code' => null,
                        'country' => null
                    ];

                    foreach ($addressComponents as $component) {
                        $types = $component['types'];
                        if (in_array('street_number', $types)) {
                            $address['street_number'] = $component['long_name'];
                        } elseif (in_array('route', $types)) {
                            $address['route'] = $component['long_name'];
                        } elseif (in_array('locality', $types)) {
                            $address['locality'] = $component['long_name'];
                        } elseif (in_array('administrative_area_level_1', $types)) {
                            $address['administrative_area'] = $component['long_name'];
                        } elseif (in_array('postal_code', $types)) {
                            $address['postal_code'] = $component['long_name'];
                        } elseif (in_array('country', $types)) {
                            $address['country'] = $component['long_name'];
                        }
                    }

                    return $address;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Reverse geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    public function getCoordinatesFromZipCode($zipCode)
    {
        // Check cache first
        $cacheKey = "zip_coordinates_{$zipCode}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $zipCode,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    
                    // Cache the result for 30 days
                    Cache::put($cacheKey, $location, now()->addDays(30));
                    
                    return $location;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
        }

        return null;
    }
} 