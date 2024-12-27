<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use App\Http\Controllers\Traits\PrestashopApi;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductTelepecasController extends Controller
{

    use PrestashopApi;

    public function index()
    {
        abort_if(Gate::denies('product_telepeca_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = $this->categories(2);

        return view('admin.productTelepecas.index', compact('categories'));
    }

    public function categoryProducts($category_id, Request $request)
    {

        $category = $this->category($category_id);

        // Obter o número da página atual da query string ou usar a primeira página por padrão
        $page = $request->query('page', 1);
        $perPage = 20;

        // Obter os produtos paginados
        $paginationData = $this->products($category_id, $page, $perPage);
        $products = $paginationData['products'];
        $totalPages = $paginationData['total_pages'];

        return view('admin.productTelepecas.products', compact('products', 'page', 'totalPages', 'category'));
    }
}
