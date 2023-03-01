@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>ערוך ארגון</h2>
        {{--<p>The most complete user interface framework that can be used to create stunning admin dashboards--}}
        {{--and presentation websites.</p>--}}
    </div>
    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('organizations.update',$organization->id) }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name"
                                   value="{{ old('name') ? old('name') : $organization->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">מְנַהֵל:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="director"
                                   value="{{ old('director') ? old('director') : $organization->director }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">מנהל טלפון:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone_director"
                                   value="{{ old('phone_director') ? old('phone_director') : $organization->phone_director }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אתר אינטרנט:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="website"
                                   value="{{ old('website') ? old('website') : $organization->website }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אימייל:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="email"
                                   value="{{ old('email') ? old('email') : $organization->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone"
                                   value="{{ old('phone') ? old('phone') : $organization->phone }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">צרף לוגו:</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" name="logo">
                            @if($organization->logo)
                                <img width="200" src="{{ '/storage/organizations/logos/'.$organization->logo }}">
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <label>מנהלים</label>
                        <div class="form-group">
                            <input type="hidden" name="managers_count" value="{{ old('manager_count') ? old('manager_count') : ($organization->managers ? $organization->managers->count() : '1') }}">
                            <div class=" manager-block" data-count="{{ old('manager_count') ? old('manager_count') : ($organization->managers ? $organization->managers->count() : '1') }}">
                                @if(old('manager_count'))
                                    @for($i = 1; $i <= old('manager_count'); $i++)
                                        <div class="row manager-block-item" data-id="{{ $i }}">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="manager_name_{{$i}}" value="{{ old('manager_name_'.$i) }}" placeholder="שם מנהל">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="manager_phone_{{$i}}" value="{{ old('manager_phone_'.$i) }}" placeholder="טלפון מנהל">
                                            </div>
                                        </div>
                                    @endfor
                                @else
                                    @if($organization->managers->count())
                                        <?php $i = 1; ?>
                                        @foreach($organization->managers as $manager)
                                            <div class="row manager-block-item" data-id="{{ $i }}">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="manager_name_{{$i}}" value="{{ $manager->name }}" placeholder="שם מנהל">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="manager_phone_{{$i}}" value="{{ $manager->phone }}" placeholder="טלפון מנהל">
                                                </div>
                                            </div>
                                        <?php $i++; ?>
                                        @endforeach
                                    @else
                                    <div class="row manager-block-item" data-id="1">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="manager_name_1" value="" placeholder="שם מנהל">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="manager_phone_1" value="" placeholder="טלפון מנהל">
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                            <button type="button" class="btn btn-info" onclick="addManagerRow()"><i class="glyph-icon icon-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary">עדכן ארגון</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/organizations.js"></script>

@endsection
