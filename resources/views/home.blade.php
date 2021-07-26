@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>מקומות תעסוקה</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
    </div>
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <a class="btn btn-info" href="{{ route('jobs.create') }}">Create a Job</a>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Organizations</label>
                            <select class="chosen-select" id="filterlistoforg">
                                <option></option>
                                @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hr's</label>
                            <select class="chosen-select" id="filterlistofhr">
                                <option></option>
                                @foreach($hrs as $hr)
                                    <option value="{{ $hr->id }}">{{ $hr->name .": ". $hr->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" id="filterlistoftype">
                                <option></option>
                                @foreach($jobTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Year</label>
                            <select class="form-control" id="filterlistofyear">
                                <option></option>
                                <option value="{{ \Carbon\Carbon::now()->format('Y') }}">{{ \Carbon\Carbon::now()->format('Y') }}</option>
                                <option value="{{ \Carbon\Carbon::now()->addYear()->format('Y') }}">{{ \Carbon\Carbon::now()->addYear()->format('Y') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="example-box-wrapper">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered jobs-datatable">
                        <thead>
                        <tr>
                            <th>השתנה</th>
                            <th>סוּג</th>
                            <th>עובדי Hr</th>
                            <th>כותרת</th>
                            <th>בית</th>
                            <th>הַחוּצָה</th>
                            <th>פְּנִימִיָה</th>
                            <th>אִרגוּן</th>
                            <th>קטגוריה</th>
                            <th>תת קטגוריה</th>
                            <th>אֵזוֹר</th>
                            <th>עִיר</th>
                            <th>גרעין</th>
                            <th>פָּעִיל</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        </thead>
                        {{--<tbody>--}}
                        {{--@foreach($jobs as $job)--}}
                            {{--<tr>--}}
                                {{--<td>{{ $job->is_admin_update ? 'כן' : 'לא' }}</td>--}}
                                {{--<td>{{ $job->type ? $job->type->name : ''}}</td>--}}
                                {{--<td><a href="{{ $job->url }}" target="_blank">{{ $job->url }}</a></td>--}}
                                {{--<td>{{ $job->hr ? implode(' ',$job->hr()->pluck('phone')->toArray()) : '' }}</td>--}}
                                {{--<td>{{ $job->title }}</td>--}}
                                {{--<td>{{ $job->home }}</td>--}}
                                {{--<td>{{ $job->out }}</td>--}}
                                {{--<td>{{ $job->dormitory }}</td>--}}
                                {{--<td>{{ $job->organization ? $job->organization->name : ''}}</td>--}}
                                {{--<td>{{ $job->category ? $job->category->name : ''}}</td>--}}
                                {{--<td>--}}
                                    {{--<form>--}}
                                        {{--<a href="{{ route('jobs.edit',$job->id) }}" class="btn btn-gray">לַעֲרוֹך</a>--}}
                                        {{--<button class="btn btn-danger">לִמְחוֹק</button>--}}
                                    {{--</form>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--@endforeach--}}
                        {{--</tbody>--}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>

@endsection
