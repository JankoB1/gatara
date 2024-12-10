<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{

    public function checkIfImageHasCoffeeCup(Request $request) {
        try {
            if(!$this->checkHeartbeat($request)) {
                return response()->json(['error' => 'Heartbeat failed'], 401);
            }
            $url = 'https://api.openai.com/v1/chat/completions';
            $imageUrl = $request->input('imageData');
            $data = [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        "role" => "user",
                        "content" => [
                            [
                                "type" => "text",
                                "text" => "Is there a coffee cup in this image that contains coffee grounds, is clearly in focus, and does not contain any other beverage or is empty? Answer only with \"yes\" or \"no\"."
                            ],
                            [
                                "type" => "image_url",
                                "image_url" => [
                                    "url" => $imageUrl
                                ]
                            ]
                        ]
                    ]
                ],
                "max_tokens" => 1,
                "temperature" => 0.02
            ];
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY')
                ],
                CURLOPT_POSTFIELDS => json_encode($data)
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $response = json_decode(curl_exec($ch), true);

            $data = [
                'isValid' => strtolower($response['choices'][0]['message']['content']) === 'yes'
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            return \response($e, 500);
        }
    }

    public function getChatGPTResponse(Request $request) {
        try {
            if(!$this->checkHeartbeat($request)) {
                return response()->json(['error' => 'Heartbeat failed'], 401);
            }
            $userData = $request->input('userData');
            $fortuneTeller = $request->input('fortuneTeller');
            $name = $userData['username'];
            $dateOfBirth = $userData['dateOfBirth'];
            $gender = $userData['gender'];
            $relationship = $userData['relationship'];
            $jobStatus = $userData['jobStatus'];

            $symbols = Symbol::inRandomOrder()->take(3)->get();

            switch ($fortuneTeller) {
                case 0:
                    $theme = 'opšteg života';
                    break;
                case 1:
                    $theme = 'ljubavi';
                    break;
                case 2:
                    $theme = 'poslovnih prilika';
                    break;
                default:
                    $theme = 'opšteg života';
            }

            switch ($jobStatus) {
                case 'employed':
                    $jobStatus = 'zaposlen';
                    break;
                case 'unemployed':
                    $jobStatus = 'nezaposlen';
                    break;
                case 'retired':
                    $jobStatus = 'u penziji';
                    break;
                case 'studying':
                    $jobStatus = 'student';
                    break;
                default:
                    $jobStatus = 'zaposlen';
            }

            switch ($gender) {
                case 'male':
                    $gender = 'muškarac';
                    break;
                case 'female':
                    $gender = 'žena';
                    break;
                case 'other':
                    $gender = 'drugo';
                    break;
                default:
                    $gender = 'muškarac';
            }

            switch ($relationship) {
                case 'single':
                    $relationship = 'samac';
                    break;
                case 'married':
                    $relationship = 'u braku';
                    break;
                case 'divorced':
                    $relationship = 'razveden';
                    break;
                case 'widowed':
                    $relationship = 'udovac';
                    break;
                case 'in relationship':
                    $relationship = 'u vezi';
                    break;
                default:
                    $relationship = 'samac';
            }

            $dobDate = new DateTime($dateOfBirth);
            $currentDate = new DateTime();

            $age = $dobDate->diff($currentDate)->y;

            $url = 'https://api.openai.com/v1/chat/completions';
            $content = 'Napiši izmišljeno proricanje sudbine na srpskom jeziku u prvom ženskom licu iz oblasti ' . $theme . '. Osoba kojoj pišeš sudbinu ima sledece informacije: ' . $age . ' godina, ' . $gender . ', ' . $relationship . ', ' . $jobStatus . '. Potrebno je da prva rečenica počinje sa "Dragi ' . $name . ' ,\n" ili "Draga ' . $name . ' ,\n" u zavisnosti od toga da li je osoba muško ili žensko. Obavezno je da postoje tačno 3 pasusa i da svaki pasus ima po 2-3 kraće rečenice. U svakom pasusu treba da \'vidis u šolji\' po jedan od simbola: ' . $symbols[0]->name . ', ' . $symbols[1]->name . ', ' . $symbols[2]->name . '. Obraćanje treba da bude u 2. licu jednine i da se izbegne veliki broj infinitiva kako bi obraćanje bilo što ličnije.';

            $data = [
                'model' => 'chatgpt-4o-latest',
                'messages' => [
                    [
                        "role" => "user",
                        "content" => $content
                    ]
                ],
//                "max_tokens" => 250,
            ];
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY')
                ],
                CURLOPT_POSTFIELDS => json_encode($data)
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $response = json_decode(curl_exec($ch), true);
            $data = [
                'response' => $response,
                'content' => $content
            ];

            $finalSymbols = [$symbols[0]->code, $symbols[1]->code, $symbols[2]->code];

            $data = [
                'aiResponse' => $response['choices'][0]['message']['content'],
                'symbols' => $finalSymbols
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            return \response($e, 500);
        }
    }

    private function checkHeartbeat(Request $request)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['error' => 'No Bearer token found'], 401);
        }

        $response = Http::withToken($bearerToken)->get('https://grandkafaapp.com/api/auth/heartbeat');

        if ($response->successful()) {
            return $response->json('heartStillBeats') ?? false;
        } else {
            return false;
        }
    }

}
