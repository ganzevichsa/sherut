@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>ארגונים</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel-body">
            <a class="btn btn-info btn-md" href="{{ route('organizations.create') }}">
                צור ארגון
            </a>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable"
                   id="categoriesList">
                <thead>
                <tr>
                    <th>שֵׁם</th>
                    <th>מְנַהֵל</th>
                    <th>מנהל טלפון</th>
                    <th>אתר אינטרנט</th>
                    <th>אימייל</th>
                    <th>טלפון</th>
                    <th>מנהלים</th>
                    <th>נוצר ב</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($organizations as $organization)
                    <tr>
                        <td>{{ $organization->name }}</td>
                        <td>{{ $organization->director }}</td>
                        <td>{{ $organization->phone_director }}</td>
                        <td>{{ $organization->website }}</td>
                        <td>{{ $organization->email }}</td>
                        <td>{{ $organization->phone }}</td>
                        <td>{{ $organization->phone }}</td>
                        <td>{{ $organization->created_at }}</td>
                        <td>
                            <form action="{{ route('organizations.destroy', $organization->id) }}" method="POST">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <a class="btn btn-gray" href="{{ route('organizations.edit',$organization->id) }}">לַעֲרוֹך</a>
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
