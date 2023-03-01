@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>Create Post</h2>
        {{--<p>The most complete user interface framework that can be used to create stunning admin dashboards--}}
        {{--and presentation websites.</p>--}}
    </div>
    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('posts.store') }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">כותרת:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="title" value="{{ old('title') ? old('title') : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">תיאור:</label>
                        <div class="col-sm-12">
                            <textarea class="summernote" name="description">{!! old('description') !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריה:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select"
                                    onchange="onChangeList('#categoriesList','#subcategoriesList')"
                                    id="categoriesList" name="category_id"
                                    data-url="{{ route('subcategories.index') }}">
                                <option></option>
                                @foreach($categories as $category)
                                    @if(old('category_id') == $category->id)
                                        <option selected value="{{ $category->id }}">{{ $category->name }}</option>
                                    @else
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריית משנה:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="subcategoriesList" data-old="{{ old('subcategory_id') ? old('subcategory_id') : '' }}" name="subcategory_id">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Roles</label>
                        <div class="col-sm-12">
                            <select multiple="multiple" data-old="{{ json_encode(old('role_ids')) }}"
                                    data-placeholder="Click to see" name="role_ids[]"
                                    class="chosen-select">
                                @foreach(\App\Role::all() as $route)
                                    @if(old('role_ids') && in_array($route->id,old('role_ids')))
                                        <option selected value="{{ $route->id }}">{{ $route->name  }}</option>
                                    @else
{{--                                        @if(!empty($post->roles()->get()->pluck('id')->toArray()) && in_array($route->id,$post->roles()->get()->pluck('id')->toArray()))--}}
{{--                                            <option selected value="{{ $route->id }}">{{ $route->name  }}</option>--}}
{{--                                        @else--}}
                                            <option value="{{ $route->id }}">{{ $route->name  }}</option>
{{--                                        @endif--}}
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Date:</label>
                        <div class="col-sm-12">
                            <input type="text" name="date" value="{{ old('date') ? old('date') : date('Y-m-d') }}" class="datepicker last_date_for_registration" data-date-format="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Image</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" name="file">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Video Url</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" value="{{ old('video_url') ? old('video_url') : '' }}" name="video_url">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">צור משרה חדשה</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
