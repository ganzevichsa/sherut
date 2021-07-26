@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>אזורים / ערים</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#home" onclick="menuTab(this)">אזורים</a></li>
                <li><a data-toggle="tab" href="#menu1" onclick="menuTab(this)">ערים</a></li>
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="panel-body">
                        <a class="btn btn-info" onclick="createRow('#areaModal','{{ route('areas.store') }}', '#areasList', true, 'areas', '', null)">צור אזור</a>
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable" id="categoriesList">
                            <thead>
                            <tr>
                                <th>שֵׁם</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($areas as $area)
                                    <tr>
                                        <td>{{ $area->name }}</td>
                                        <td>
                                            <form action="{{ route('areas.destroy', $area->id) }}" method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a class="btn btn-gray" onclick="createRow('#areaModal','{{ route('areas.store') }}', '#areasList', true, 'areas', '{{ $area->id }}', null)">לַעֲרוֹך</a>
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
                            onclick="createRow('#cityModal','{{ route('cities.store') }}', '#citiesList', true, 'cities', null, '')">
                        צור עיר
                    </button>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered datatable" id="categoriesList">
                        <thead>
                        <tr>
                            <th>שֵׁם</th>
                            <th>אֵזוֹר</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cities as $city)
                            <tr>
                                <td>{{ $city->name }}</td>
                                <td>{{ $city->area ? $city->area->name : '' }}</td>
                                <td>
                                    <form action="{{ route('cities.destroy', $city->id) }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <a class="btn btn-gray" onclick="createRow('#cityModal','{{ route('cities.store') }}', '#citiesList', true, 'cities', '{{ $city->id }}', 'area_id')">לַעֲרוֹך</a>
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
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
