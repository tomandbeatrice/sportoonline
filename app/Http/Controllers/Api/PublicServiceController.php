<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceModule; // Assuming a generic model or specific ones if they exist

class PublicServiceController extends Controller
{
    private function getToursData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Kapadokya Balon Turu',
                'location' => 'Nevşehir',
                'price' => 3500,
                'rating' => 4.8,
                'reviews' => 124,
                'image' => 'https://images.unsplash.com/photo-1641128324972-af3212f0f6bd?q=80&w=1000&auto=format&fit=crop',
                'duration' => '3 Gün',
                'dates' => ['2025-04-15', '2025-04-20'],
                'description' => 'Kapadokya\'nın eşsiz doğasında unutulmaz bir balon turu deneyimi. Sabahın ilk ışıklarıyla gökyüzüne yükselin.'
            ],
            [
                'id' => 2,
                'title' => 'Likya Yolu Yürüyüşü',
                'location' => 'Antalya',
                'price' => 2800,
                'rating' => 4.9,
                'reviews' => 85,
                'image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=1000&auto=format&fit=crop',
                'duration' => '5 Gün',
                'dates' => ['2025-05-01', '2025-05-10'],
                'description' => 'Dünyanın en iyi yürüyüş rotalarından biri olan Likya Yolu\'nda tarih ve doğa ile iç içe bir macera.'
            ]
        ];
    }

    private function getCarsData()
    {
        return [
            [
                'id' => 1,
                'model' => 'Fiat Egea',
                'type' => 'Sedan',
                'transmission' => 'Otomatik',
                'fuel' => 'Dizel',
                'price_per_day' => 1200,
                'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=1000&auto=format&fit=crop',
                'supplier' => 'Garenta',
                'features' => ['Klima', 'Bluetooth', 'Hız Sabitleyici']
            ],
            [
                'id' => 2,
                'model' => 'Renault Clio',
                'type' => 'Hatchback',
                'transmission' => 'Manuel',
                'fuel' => 'Benzin',
                'price_per_day' => 950,
                'image' => 'https://images.unsplash.com/photo-1621007947382-bb3c3968e3bb?q=80&w=1000&auto=format&fit=crop',
                'supplier' => 'Avis',
                'features' => ['Klima', 'USB', 'Park Sensörü']
            ]
        ];
    }

    private function getInsuranceData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Seyahat Sağlık Sigortası',
                'provider' => 'Allianz',
                'coverage' => '50.000 EUR',
                'price' => 450,
                'features' => ['Covid-19 Kapsamı', 'Bagaj Kaybı', 'Tıbbi Tedavi'],
                'description' => 'Yurt dışı seyahatlerinizde karşılaşabileceğiniz sağlık sorunlarına karşı tam güvence.'
            ],
            [
                'id' => 2,
                'title' => 'Ekstrem Sporlar Sigortası',
                'provider' => 'Axa',
                'coverage' => '100.000 TL',
                'price' => 750,
                'features' => ['Yamaç Paraşütü', 'Dalış', 'Kayak'],
                'description' => 'Adrenalin tutkunları için özel olarak hazırlanmış, riskli sporları kapsayan sigorta paketi.'
            ]
        ];
    }

    private function getActivitiesData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Yoga Workshop',
                'instructor' => 'Zeynep Aksoy',
                'date' => '2025-03-20 10:00',
                'location' => 'Online',
                'price' => 250,
                'image' => 'https://images.unsplash.com/photo-1599901860904-17e6ed7083a0?q=80&w=1000&auto=format&fit=crop',
                'description' => 'Zihin ve beden dengenizi bulacağınız, her seviyeye uygun online yoga atölyesi.'
            ],
            [
                'id' => 2,
                'title' => 'Tenis Turnuvası',
                'location' => 'Kemer Country Club',
                'date' => '2025-04-05 09:00',
                'price' => 500,
                'image' => 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?q=80&w=1000&auto=format&fit=crop',
                'description' => 'Amatör tenisçiler için düzenlenen, keyifli ve rekabet dolu bir hafta sonu turnuvası.'
            ]
        ];
    }

    public function tours(Request $request)
    {
        return response()->json(['data' => $this->getToursData()]);
    }

    public function showTour($id)
    {
        $item = collect($this->getToursData())->firstWhere('id', (int)$id);
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item]);
    }

    public function cars(Request $request)
    {
        return response()->json(['data' => $this->getCarsData()]);
    }

    public function showCar($id)
    {
        $item = collect($this->getCarsData())->firstWhere('id', (int)$id);
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item]);
    }

    public function insurance(Request $request)
    {
        return response()->json(['data' => $this->getInsuranceData()]);
    }

    public function showInsurance($id)
    {
        $item = collect($this->getInsuranceData())->firstWhere('id', (int)$id);
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item]);
    }

    public function activities(Request $request)
    {
        return response()->json(['data' => $this->getActivitiesData()]);
    }

    public function showActivity($id)
    {
        $item = collect($this->getActivitiesData())->firstWhere('id', (int)$id);
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item]);
    }

    public function search(Request $request)
    {
        $query = strtolower($request->input('q', ''));
        $results = [];

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        // Search Tours
        foreach ($this->getToursData() as $tour) {
            if (str_contains(strtolower($tour['title']), $query) || str_contains(strtolower($tour['location']), $query)) {
                $results[] = [
                    'id' => $tour['id'],
                    'type' => 'tour',
                    'title' => $tour['title'],
                    'subtitle' => $tour['location'],
                    'image' => $tour['image'],
                    'price' => $tour['price'],
                    'url' => '/tours/' . $tour['id']
                ];
            }
        }

        // Search Cars
        foreach ($this->getCarsData() as $car) {
            $brand = $car['brand'] ?? '';
            $title = $brand ? ($brand . ' ' . $car['model']) : $car['model'];
            if (str_contains(strtolower($title), $query) || str_contains(strtolower($car['type']), $query)) {
                $results[] = [
                    'id' => $car['id'],
                    'type' => 'car',
                    'title' => $title,
                    'subtitle' => $car['type'] . ' - ' . $car['transmission'],
                    'image' => $car['image'],
                    'price' => $car['price_per_day'],
                    'url' => '/cars/' . $car['id']
                ];
            }
        }

        // Search Insurance
        foreach ($this->getInsuranceData() as $ins) {
            if (str_contains(strtolower($ins['title']), $query) || str_contains(strtolower($ins['provider']), $query)) {
                $results[] = [
                    'id' => $ins['id'],
                    'type' => 'insurance',
                    'title' => $ins['title'],
                    'subtitle' => $ins['provider'],
                    'price' => $ins['price'],
                    'url' => '/insurance/' . $ins['id']
                ];
            }
        }

        // Search Activities
        foreach ($this->getActivitiesData() as $act) {
            if (str_contains(strtolower($act['title']), $query) || str_contains(strtolower($act['location']), $query)) {
                $results[] = [
                    'id' => $act['id'],
                    'type' => 'activity',
                    'title' => $act['title'],
                    'subtitle' => $act['location'] . ' - ' . $act['date'],
                    'image' => $act['image'],
                    'price' => $act['price'],
                    'url' => '/activities/' . $act['id']
                ];
            }
        }

        return response()->json(['data' => $results]);
    }
}
