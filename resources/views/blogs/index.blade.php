@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>Blogs</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel-body">
            <a class="btn btn-info btn-md" href="{{ route('blogs.create') }}">
                צור ארגון
            </a>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable"
                   id="categoriesList">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($blogs as $blog)
                    <tr>
                        <td>{{ $blog->title }}</td>
                        <td>{{ $blog->category->name . ", " . $blog->subcategory->name}}</td>
                        <td>{{ $blog->date }}</td>
                        <td>
                            <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <a class="btn btn-gray" href="{{ route('blogs.edit',$blog->id) }}">לַעֲרוֹך</a>
                                <button class="btn btn-danger">לִמְחוֹק</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
