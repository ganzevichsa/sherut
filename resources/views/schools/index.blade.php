@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>Schools</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel">
            <h3>מוסד</h3>
            <div class="panel-body">
                <a class="btn btn-info" onclick="createRow('#schoolModal','{{ route('schools.store') }}', '#schoolsList', true, 'schools', '', null)">צור אזור</a>
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable" id="categoriesList">
                    <thead>
                    <tr>
                        <th>שֵׁם</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schools as $school)
                        <tr>
                            <td>{{ $school->name }}</td>
                            <td>
                                <form action="{{ route('schools.destroy', $school->id) }}" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <a class="btn btn-gray" onclick="createRow('#schoolModal','{{ route('schools.store') }}', '#schoolsList', true, 'schools', '{{ $school->id }}', null)">לַעֲרוֹך</a>
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
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
