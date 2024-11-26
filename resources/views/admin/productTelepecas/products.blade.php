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
                    <th>Referência</th>
                    <th>Fabricante</th>
                    <th>Preço</th>
                    <th>Imagem</th>
                    <th style="text-align: right"><input type="checkbox" id="checkbox_all" onclick=changeSelection()></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name[0]->value }}</td>
                    <td>{{ $product->reference }}</td>
                    <td>{{ $product->manufacturer_name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>
                        @if (isset($product->associations->images))
                        https://autorcpecas.pt/{{ $product->associations->images[0]->id }}-large_default/{{ $product->link_rewrite[0]->value }}.jpg
                        @endif
                    </td>
                    <td style="text-align: right">
                        <input type="checkbox" id="checkbox_{{ $product->id }}" data-checkbox="{{ $product->id }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $page - 1 }}">Anterior</a>
                </li>
                @for ($i = 1; $i <= $totalPages; $i++) <li class="page-item {{ $i == $page ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                    </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $page + 1 }}">Próximo</a>
                    </li>
            </ul>
        </nav>
    </div>
</div>

@endsection
@section('scripts')
<script>
    changeSelection = () => {
        let checkbox_all = $('#checkbox_all').prop('checked');
        $('input[type="checkbox"]').prop('checked', checkbox_all);
    }

</script>
@endsection
