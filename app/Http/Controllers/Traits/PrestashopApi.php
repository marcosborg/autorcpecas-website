<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Str;

trait PrestashopApi
{

    public function categories($category_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('PRESTASHOP_WEBSITE') . '/api/categories?language=1&output_format=JSON&display=full&filter[id_parent]=' . $category_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY')
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public function category($category_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://autorcpecas.pt/api/categories/' . $category_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function manufacturers()
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('PRESTASHOP_WEBSITE') . '/api/manufacturers?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function manufacturer($manufacturer_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('PRESTASHOP_WEBSITE') . '/api/manufacturers/' . $manufacturer_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function newProduct($request)
    {
        $html = '';
        foreach ($request->categories as $category) {
            $html .= '<category>';
            $html .= '<id>' . $category['id'] . '</id>';
            $html .= '</category>';
        }

        $xmlPayload = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
        <product>
            <id_manufacturer>' . $request->manufacturer_id . '</id_manufacturer>
            <id_category_default>' . $request->default_category . '</id_category_default>
            <id_tax_rules_group>111</id_tax_rules_group>
            <type>simple</type>
            <id_shop_default>1</id_shop_default>
            <reference>' . $request->reference . '</reference>
            <state>1</state>
            <price>' . $request->price . '</price>
            <active>0</active>
            <show_price>1</show_price>
            <visibility>both</visibility>
            <meta_description>
                <language id="1">' . $request->name_pt . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="2">' . $request->name_es . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="3">' . $request->name_en . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
            </meta_description>
            <meta_title>
                <language id="1">' . $request->name_pt . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="2">' . $request->name_es . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="3">' . $request->name_en . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
            </meta_title>
            <link_rewrite>
                <language id="1">' . Str::slug($request->name_pt) . '</language>
                <language id="2">' . Str::slug($request->name_es) . '</language>
                <language id="3">' . Str::slug($request->name_en) . '</language>
            </link_rewrite>
            <name>
                <language id="1">' . $request->name_pt . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="2">' . $request->name_es . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
                <language id="3">' . $request->name_en . ($request->part_name !== '' ? ' - ' . $request->part_name : '') . '</language>
            </name>
            <description>
                <language id="1">' . $request->references . '</language>
                <language id="2">' . $request->references . '</language>
                <language id="3">' . $request->references . '</language>
            </description>
            <associations>
                <categories>' . $html . '</categories>
            </associations>
        </product>
    </prestashop>';

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('PRESTASHOP_WEBSITE') . '/api/products',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $xmlPayload,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/xml',
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error_msg], 500);
        }

        curl_close($curl);

        // Parse the XML response
        $xmlObject = simplexml_load_string($response);

        // Check if the XML response is invalid
        if ($xmlObject === false) {
            return response()->json(['error' => 'Invalid XML response'], 500);
        }

        // Extract error messages if present
        $errorMessages = [];
        if (isset($xmlObject->errors)) {
            foreach ($xmlObject->errors->error as $error) {
                $errorMessages[] = (string) $error->message;
            }
        }

        // Check if the XML response contains the expected structure
        if (!isset($xmlObject->product->id)) {
            return response()->json(['error' => 'Product ID not found in response', 'messages' => $errorMessages], 500);
        }

        // Extract the product ID
        $id = (string) $xmlObject->product->id;

        // Return the ID and any error messages as a JSON response
        return response()->json(['id' => $id, 'warnings' => $errorMessages], 200);
    }

    public function product($prestashop_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => env('PRESTASHOP_WEBSITE') . '/api/products/' . $prestashop_id . '?output_format=JSON&display=full',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('PRESTASHOP_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function products($category_id, $page, $perPage)
    {
        // Obter dados da categoria
        $data = $this->category($category_id);
        $products = [];

        if (!isset($data['categories'][0]['associations']['products'])) {
            return [
                'products' => $products,
                'page' => $page,
                'total_pages' => 0,
            ];
        }

        // Obter a lista de produtos associados
        $allProducts = $data['categories'][0]['associations']['products'];

        // Calcular o total de produtos e páginas
        $totalProducts = count($allProducts);
        $totalPages = ceil($totalProducts / $perPage);

        // Calcular o índice de início e final da paginação
        $start = ($page - 1) * $perPage;
        $end = min($start + $perPage, $totalProducts);

        // Garantir que o índice não ultrapasse o total de produtos
        if ($start >= $totalProducts) {
            return [
                'products' => $products,
                'page' => $page,
                'total_pages' => $totalPages,
            ];
        }

        // Iterar apenas pelos produtos da página atual
        for ($i = $start; $i < $end; $i++) {
            $productId = $allProducts[$i]['id'];
            $result = $this->product($productId);

            if (isset($result->products[0])) {
                $products[] = $result->products[0];
            }
        }

        return [
            'products' => $products,
            'page' => $page,
            'total_pages' => $totalPages,
        ];
    }
}
