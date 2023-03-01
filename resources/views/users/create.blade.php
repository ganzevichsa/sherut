@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>צור משתמש</h2>
        {{--<p>The most complete user interface framework that can be used to create stunning admin dashboards--}}
        {{--and presentation websites.</p>--}}
    </div>
    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('users.store') }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">תפקידים:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" name="role_id">
                                <option></option>
                                @foreach($roles as $role)
                                    @if(old('role_id') == $role->id)
                                        <option selected value="{{ $role->id }}">{{ $role->name }}</option>
                                    @else
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">ארגונים:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" name="organization_id">
                                <option></option>
                                @foreach($organizations as $organization)
                                    @if(old('organization_id') == $organization->id)
                                        <option selected value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @else
                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') ? old('phone') : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אימייל:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="email" value="{{ old('email') ? old('email') : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">צרף תמונת פרופיל:</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" name="avatar">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">צרף עבודות</label>
                        <div class="col-sm-12">
                            <select multiple="multiple" id="hrsList"
                                    data-placeholder="לחץ כדי לראות ולצרף עבודות" name="job_ids[]"
                                    class="chosen-select">
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">צור משתמש</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
