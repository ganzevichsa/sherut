@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>קטגוריות / קטגוריות משנה</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" onclick="menuTab(this)" href="#home">קטגוריות</a></li>
                <li><a data-toggle="tab" onclick="menuTab(this)" href="#menu1">קטגוריות משנה</a></li>
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="panel-body">
                        <a class="btn btn-info" onclick="createRow('#categoryModal','{{ route('categories.store') }}', '#categoriesList', true, 'categories','',null)">צור קטגוריה</a>
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable tablesorter" id="categoriesList">
                            <thead>
                            <tr>
                                <th class="sort-cat">שֵׁם</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr class="sort-items-cat">
                                        <td data-name="{{ $category->name }}">{{ $category->name }}</td>
                                        <td>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-gray" onclick="createRow('#categoryModal','{{ route('categories.store') }}', '#categoriesList', true, 'categories', '{{$category->id}}', null, '{{ $category->value }}')">לַעֲרוֹך</a>
                                                <button class="btn btn-danger">לִמְחוֹק</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <button class="btn btn-info btn-md"
                            onclick="createRow('#subCategoryModal','{{ route('subcategories.store') }}', '#subcategoriesList', true, 'subcategories','',null)">
                        צור תת קטגוריה
                    </button>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable dataTable" id="subCategoriesList">
                        <thead>
                        <tr>
                            <th>שֵׁם</th>
                            <th class="sort-sub-cat sorting">קטגוריה</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="table1">
                        @foreach($subcategories as $subcategory)
                            <tr class="sort-items-cat">
                                <td>{{ $subcategory->name }}</td>
                                <td class="sort-cat-item">{{ $subcategory->category ? $subcategory->category->name : '' }}</td>
                                <td>
                                    <form action="{{ route('subcategories.destroy', $subcategory->id) }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <a class="btn btn-gray" onclick="createRow('#subCategoryModal','{{ route('subcategories.store') }}', '#subcategoriesList', true, 'subcategories', '{{$subcategory->id}}', 'category_id', '{{ $category->value }}')">לַעֲרוֹך</a>
                                        <button class="btn btn-danger">לִמְחוֹק</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <input type="hidden" id="sub_order" value="asc">
    <script type="text/javascript" src="/js/jobs.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.sort-cat', function(){
            var mylist = $('#categoriesList');
            var listitems = mylist.find('tr.sort-items-cat');
            if($(this).hasClass('sorting_a')){
                $(this).removeClass('sorting_a');
                $(this).addClass('sorting_b');
                listitems.sort(function(a, b) {
                    return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
                })
            }else{
                $(this).removeClass('sorting_b');
                $(this).addClass('sorting_a');
                listitems.sort(function(a, b) {
                    return $(b).text().toUpperCase().localeCompare($(a).text().toUpperCase());
                })
            }
            
            $.each(listitems, function(idx, itm) { mylist.append(itm); });
        });

        $(document).on('click', '.sort-sub-cat', function(){
            var table=$('#subCategoriesList');
            var tbody =$('#table1');

            tbody.find('tr').sort(function(a, b) 
            {
                if($('#sub_order').val()=='asc') 
                {
                    return $('td.sort-cat-item', a).text().localeCompare($('td.sort-cat-item', b).text());
                }
                else 
                {
                    return $('td.sort-cat-item', b).text().localeCompare($('td.sort-cat-item', a).text());
                }
        
            }).appendTo(tbody);
    
            var sort_order=$('#sub_order').val();
            if(sort_order == "asc")
            {
                $('#sub_order').val('desc');
            }
            
            if(sort_order=="desc")
            {
                $('#sub_order').val('asc');
            }
        });
        
    </script>

@endsection
