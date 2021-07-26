@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>ערוך משתמש</h2>
        {{--<p>The most complete user interface framework that can be used to create stunning admin dashboards--}}
        {{--and presentation websites.</p>--}}
    </div>
    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <h4>מקומות תעסוקה</h4>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td>שֵׁם</td>
                        <td>כתובת אתר</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tbody>
                    @foreach($user->jobs as $job)
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td><a href="{{ route('jobs.edit',$job->id) }}"
                                   target="_blank">{{ route('jobs.edit',$job->id) }}</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                    </tbody>
                </table>
                <form action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $user->name }}">
                        </div>
                    </div>
                    @if($user->jobs->count())
                        <p style="font-size: 20px;color: red;">למשתמש זה כבר יש משרות מחוברות, כך שלא תוכלו לשנות את
                            תפקידו או את הארגון שלו</p>
                    @else
                    <div class="form-group">
                        <label class="control-label">תפקידים:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" name="role_id">
                                <option></option>
                                @foreach($roles as $role)
                                    @if(old('role_id') == $role->id)
                                        <option selected value="{{ $role->id }}">{{ $role->name }}</option>
                                    @else
                                        @if($user->role_id == $role->id)
                                        <option selected value="{{ $role->id }}">{{ $role->name }}</option>
                                        @else
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
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
                                        @if($user->organization_id == $organization->id)
                                        <option selected value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @else
                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="control-label">טלפון:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') ? old('phone') : $user->phone }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אימייל:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="email" value="{{ old('email') ? old('email') : $user->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">צרף תמונת פרופיל:</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" name="avatar">
                            @if($user->avatar)
                                <img width="200" src="{{ '/storage/users/avatars/'.$user->avatar }}">
                            @endif
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
                        <button class="btn btn-primary">ערוך משתמש</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
