@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>Years</h2>
        @if(session('message'))
            <p style="color: #b245f7;font-size: 35px;">{{ session('message') }}</p>
        @endif
        {{--<p>The most complete user interface framework that can be used to create stunning admin dashboards--}}
        {{--and presentation websites.</p>--}}
    </div>

    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('years.store') }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label class="control-label">This year: </label>
                        <div class="col-sm-12">
                            <input class="form-control" value="{{ old('this_year') ? old('this_year') : $years->where('key','this_year')->first()->name}}" name="this_year">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Next year: </label>
                        <div class="col-sm-12">
                            <input class="form-control" value="{{ old('next_year') ? old('next_year') : $years->where('key','next_year')->first()->name }}" name="next_year">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">צור ארגון</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/quiz.js"></script>

@endsection
