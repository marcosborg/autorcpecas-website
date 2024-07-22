<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Controllers\Traits\PrestashopApi;
;

class AuthController extends Controller
{

    use PrestashopApi;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => [
                        'Invalid credentials'
                    ],
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $authToken,
        ]);
    }

    public function sendPhoto(Request $request)
    {

        $base64String = $request->imageUrl;

        if (strpos($base64String, 'data:image/') === 0) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }

        $imageData = base64_decode($base64String);

        $fileName = Str::random(10) . '.jpg';

        $filePath = public_path('storage/images/' . $fileName);

        File::put($filePath, $imageData);

        //$image = 'https://ai.autorcpecas.pt/images/' . $fileName;

        $image = 'https://ai.airbagszentrum.com/images/aR5nNSA8Ma.jpg';

        $url = 'https://api.ocr.space/parse/imageurl?apikey=' . env('OCR_API_HEY') . '&url=' . $image;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $ocrText = json_decode($response)->ParsedResults[0]->ParsedText;

        // Separar as linhas
        $lines = explode("\n", $ocrText);

        // Remover espaços extras de cada linha
        $cleanedLines = array_map('trim', $lines);

        // Opcional: Remover linhas vazias, se houver
        $cleanedLines = array_filter($cleanedLines, function ($line) {
            return !empty($line);
        });

        // Resetar as chaves do array (opcional, se precisar de um array indexado)
        $cleanedLines = array_values($cleanedLines);

        // Remover todos os espaços de cada item do array
        $cleanedArray = array_map(function ($item) {
            return str_replace(' ', '', $item);
        }, $cleanedLines);

        // Filtrar o array usando a função isValidReference
        $filteredArray = array_filter($cleanedArray, [$this, 'isValidReference']);

        // Reindexar o array
        $filteredArray = array_values($filteredArray);

        // Exibir o resultado
        return response()->json($filteredArray);
    }

    public function isValidReference($item)
    {

        // Verificar se o item tem mais de 5 caracteres e menos de 14 caracteres
        $length = strlen($item);
        if ($length <= 4 || $length >= 30) {
            return false;
        }

        /*

        // Verificar se o item contém apenas letras e números
        if (preg_match('/[^A-Za-z0-9]/', $item)) {
            return false;
        }

        */

        return true;
    }

    public function getCategories($category_id)
    {
        return $this->categories($category_id);
    }

    public function getCategory($category_id)
    {
        return $this->category($category_id);
    }

    public function getManufacturers()
    {
        return $this->manufacturers();
    }

    public function createProduct(Request $request)
    {

        return $this->newProduct($request);

    }

    public function getManufacturer($manufacturer_id)
    {
        return $this->manufacturer($manufacturer_id);
    }
}
