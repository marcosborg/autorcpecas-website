@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Categorias
    </div>

    <div class="card-body">
        <table class="table table-striped datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Produtos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories['categories'] as $category)
                <tr>
                    <td>{{ $category['id'] }}</td>
                    <td>{{ $category['name'] }}</td>
                    <td>{{ $category['nb_products_recursive'] }}</td>
                    <td><a href="/admin/product-telepecas/category-products/{{ $category['id'] }}" class="btn btn-primary btn-sm">Produtos</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



@endsection