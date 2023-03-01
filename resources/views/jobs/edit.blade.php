@extends('layouts.main')

@section('content')
    <div id="page-title">
        <h2>ערוך עבודה</h2>
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
                <form action="{{ route('jobs.update',$job->id) }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">כותרת:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="title" value="{{ old('title') ? old('title') : $job->title }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריה:</label>
                        <button type="button" class="btn btn-default btn-md"
                                onclick="createRow('#categoryModal','{{ route('categories.store') }}', '#categoriesList')">
                            Create Category
                        </button>
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
                                        @if($job->category_id == $category->id)
                                        <option selected value="{{ $category->id }}">{{ $category->name }}</option>
                                        @else
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריית משנה:</label>
                        <button class="btn btn-default btn-md" type="button"
                                onclick="createRow('#subCategoryModal','{{ route('subcategories.store') }}', '#subcategoriesList')">
                            צור תת קטגוריה
                        </button>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="subcategoriesList" data-old="{{ old('subcategory_id') ? old('subcategory_id') : $job->subcategory_id }}" name="subcategory_id">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">אזורים:</label>
                        <button type="button" class="btn btn-default btn-md"
                                onclick="createRow('#areaModal','{{ route('areas.store') }}', '#areasList')">צור אזור
                        </button>
                        <div class="col-sm-12">
                            <select class="chosen-select" onchange="onChangeList('#areasList','#citiesList')"
                                    id="areasList"
                                    name="area_id"
                                    data-url="{{ route('cities.index') }}">
                                <option></option>
                                @foreach($areas as $area)
                                    @if(old('area_id') == $area->id)
                                        <option selected value="{{ $area->id }}">{{ $area->name }}</option>
                                    @else
                                        @if($job->city && $job->city->area && $job->city->area->id == $area->id)
                                        <option selected value="{{ $area->id }}">{{ $area->name }}</option>
                                        @else
                                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">כתובות:</label>
                        <button type="button" class="btn btn-default btn-md"
                                onclick="createRow('#addressModal','{{ route('addresses.store') }}', '#addressList')">
                            צור כתובת
                        </button>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="addressList" name="address_id">
                                <option></option>
                                @foreach($addresses as $address)
                                    @if(old('address_id') == $address->id)
                                        <option selected value="{{ $address->id }}">{{ $address->name }}</option>
                                    @else
                                        @if($job->address_id == $address->id)
                                            <option selected value="{{ $address->id }}">{{ $address->name }}</option>
                                        @else
                                            <option value="{{ $address->id }}">{{ $address->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">ערים:</label>
                        <button class="btn btn-default btn-md" type="button"
                                onclick="createRow('#cityModal','{{ route('cities.store') }}', '#citiesList')">
                            צור עיר
                        </button>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="citiesList" data-old="{{ old('city_id') ? old('city_id') : $job->city_id }}" name="city_id">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">עובדי משאבי אנוש:</label>
                        <button type="button" class="btn btn-default btn-md"
                                onclick="createRow('#hrModal','{{ route('users.store') }}', '#hrsList')">צור עובד Hr
                        </button>
                        <div class="col-sm-12">
                            <select multiple="multiple" id="hrsList" data-old="{{ old('hr_id') ? json_encode(old('hr_id')) : json_encode($job->hr->pluck('id')->toArray()) }}"
                                    data-placeholder="Click to see hr's" name="hr_id[]"
                                    class="chosen-select">
                                @foreach(\App\User::where('role_id',\App\Role::HR)->get() as $hr)
                                    @if(old('hr_id') && in_array($hr->id,old('hr_id')))
                                        <option selected value="{{ $hr->id }}">{{ $hr->name.": ".$hr->phone }}</option>
                                    @else
                                        @if($job->hr()->count() && in_array($hr->id,$job->hr()->pluck('id')->toArray()))
                                        <option selected value="{{ $hr->id }}">{{ $hr->name.": ".$hr->phone }}</option>
                                        @else
                                        <option value="{{ $hr->id }}">{{ $hr->name.": ".$hr->phone }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">סוּג:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="typeList" name="job_type_id">
                                <option></option>
                                @foreach($types as $type)
                                    @if(old('type_id') == $type->id)
                                        <option selected value="{{ $type->id }}">{{ $type->name }}</option>
                                    @else
                                        @if($job->job_type_id == $type->id)
                                        <option selected value="{{ $type->id }}">{{ $type->name }}</option>
                                        @else
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="midrasha-block" style="background: #e0e0e0;display: {{old('type_id') == \App\JobType::MIDRASHA ? 'block' : ($job->job_type_id == \App\JobType::MIDRASHA ? 'block' : 'none')}}">
                        <h3>מידע על מדרשה</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">תכנית:</label>
                                    <div class="col-sm-12">
                                        <select class="chosen-select" name="program">
                                            <option></option>
                                            @foreach($programs as $program)
                                                @if(old('program') == $program)
                                                    <option selected value="{{ $program }}">{{ $program }}</option>
                                                @else
                                                    @if($job->program == $program)
                                                        <option selected value="{{ $program }}">{{ $program }}</option>
                                                    @else
                                                        <option value="{{ $program }}">{{ $program }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">קהל יעד:</label>
                                    <div class="col-sm-12">
                                        <select class="chosen-select" name="target_audience">
                                            <option></option>
                                            @foreach($target_audience as $audience)
                                                @if(old('target_audience') == $audience)
                                                    <option selected value="{{ $audience }}">{{ $audience }}</option>
                                                @else
                                                    @if($job->target_audience == $audience)
                                                        <option selected value="{{ $audience }}">{{ $audience }}</option>
                                                    @else
                                                        <option value="{{ $audience }}">{{ $audience }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">מסלול:</label>
                                    <div class="col-sm-12">
                                        <select class="chosen-select" name="midrasha_route">
                                            <option></option>
                                            @foreach($route_midrasha as $route)
                                                @if(old('midrasha_route') == $route)
                                                    <option selected value="{{ $route }}">{{ $route }}</option>
                                                @else
                                                    @if($job->midrasha_route == $route)
                                                        <option selected value="{{ $route }}">{{ $route }}</option>
                                                    @else
                                                        <option value="{{ $route }}">{{ $route }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">קטגוריות:</label>
                                    <div class="inputs">
                                        @if(old('midrasha_info') && old('midrasha_info')['categories'])
                                            @foreach(old('midrasha_info')['categories'] as $categoory)
                                                <input type="text" class="form-control" name="midrasha_info[categories][]" value="{{$categoory}}">
                                            @endforeach
                                        @else
                                            @if($job->midrasha)
                                                @if(!empty($job->midrasha->categories))
                                                    @foreach(json_decode($job->midrasha->categories, true) as $category)
                                                        <input type="text" class="form-control" name="midrasha_info[categories][]" value="{{$category}}">
                                                    @endforeach
                                                @else
                                                <input type="text" class="form-control" name="midrasha_info[categories][]" value="">
                                                @endif
                                            @else
                                            <input type="text" class="form-control" name="midrasha_info[categories][]" value="">
                                            @endif
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="addNewInput(this)"><i class="glyph-icon icon-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Main Info:</label>
                                    <div class="inputs">
                                        @if(old('midrasha_info') && old('midrasha_info')['main_info'])
                                            @foreach(old('midrasha_info')['main_info'] as $main_info)
                                                <input type="text" class="form-control" name="midrasha_info[main_info][]" value="{{$main_info}}">
                                            @endforeach
                                        @else
                                            @if($job->midrasha)
                                                @if(!empty($job->midrasha->main_info))
                                                    @foreach(json_decode($job->midrasha->main_info, true) as $main_info)
                                                        <input type="text" class="form-control" name="midrasha_info[main_info][]" value="{{ $main_info }}">
                                                    @endforeach
                                                @else
                                                    <input type="text" class="form-control" name="midrasha_info[main_info][]" value="">
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="midrasha_info[main_info][]" value="">
                                            @endif
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="addNewInput(this)"><i class="glyph-icon icon-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">פִּי:</label>
                                    <div class="inputs">
                                        @if(old('midrasha_info') && old('midrasha_info')['times'])
                                            @foreach(old('midrasha_info')['times'] as $times)
                                                <input type="text" class="form-control" name="midrasha_info[times][]" value="{{$times}}">
                                            @endforeach
                                        @else
                                            @if($job->midrasha)
                                                @if(!empty($job->midrasha->times))
                                                    @foreach(json_decode($job->midrasha->times, true) as $times)
                                                        <input type="text" class="form-control" name="midrasha_info[times][]" value="{{ $times }}">
                                                    @endforeach
                                                @else
                                                    <input type="text" class="form-control" name="midrasha_info[times][]" value="">
                                                @endif
                                            @else
                                            <input type="text" class="form-control" name="midrasha_info[times][]" value="">
                                            @endif
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="addNewInput(this)"><i class="glyph-icon icon-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">תנאים:</label>
                                    <div class="inputs">
                                        @if(old('midrasha_info') && old('midrasha_info')['terms'])
                                            @foreach(old('midrasha_info')['terms'] as $terms)
                                                <input type="text" class="form-control" name="midrasha_info[terms][]" value="{{$terms}}">
                                            @endforeach
                                        @else
                                            @if($job->midrasha)
                                                @if(!empty($job->midrasha->terms))
                                                    @foreach(json_decode($job->midrasha->terms, true) as $terms)
                                                        <input type="text" class="form-control" name="midrasha_info[terms][]" value="{{ $terms }}">
                                                    @endforeach
                                                @else
                                                    <input type="text" class="form-control" name="midrasha_info[terms][]" value="">
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="midrasha_info[terms][]" value="">
                                            @endif
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="addNewInput(this)"><i class="glyph-icon icon-plus"></i></button>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">אזורים:</label>
                                    <div class="col-sm-12">
                                        <textarea class="summernote" name="midrasha_info[areas]">{{ old('midrasha_info') && old('midrasha_info')['areas'] ? old('midrasha_info')['areas'] : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">מספר משרות פעיל:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="home" value="{{ old('home') ? old('home') : $job->home }}" placeholder="תקן בית">
                            <input type="text" class="form-control" name="out" value="{{ old('out') ? old('out') : $job->out }}" placeholder="דירת שירות">
                            <input type="text" class="form-control" name="dormitory" value="{{ old('dormitory') ? $job->dormitory : '' }}" placeholder="פנימיה">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אופן מיונים :</label>
                        <div class="col-sm-12">
                            <select name="how_to_sort" class="form-control">
                                <option></option>
                                @foreach($howToSort as $sort)
                                    @if(old('how_to_sort') == $sort)
                                        <option selected value="{{ $sort }}">{{ $sort }}</option>
                                    @else
                                        @if($job->how_to_sort == $sort)
                                        <option selected value="{{ $sort }}">{{ $sort }}</option>
                                        @else
                                        <option value="{{ $sort }}">{{ $sort }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">גרעין :</label>
                        <div class="col-sm-12">
                            <select name="nucleus" class="form-control">
                                <option></option>
                                @foreach($nucleus as $nucleus)
                                    @if(old('nucleus') == $nucleus)
                                        <option selected value="{{ $nucleus }}">{{ $nucleus }}</option>
                                    @else
                                        @if($job->nucleus == $nucleus)
                                        <option selected value="{{ $nucleus }}">{{ $nucleus }}</option>
                                        @else
                                        <option value="{{ $nucleus }}">{{ $nucleus }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">על אודות:</label>
                        <div class="col-sm-12">
                            <textarea class="summernote" name="about">{!! old('about') ? old('about') : $job->about !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">תיאור:</label>
                        <div class="col-sm-12">
                            <textarea class="summernote" name="description">{!! old('description') ? old('description') : $job->description !!}</textarea>
                        </div>
                    </div>

                    {{--<div class="form-group">--}}
                        {{--<label class="control-label">מצב החינוך::</label>--}}
                        {{--<button type="button" class="btn btn-default btn-md"--}}
                                {{--onclick="createRow('#stageOfEducationModal','{{ route('stageOfEducations.store') }}', '#stageOfEducationList')">--}}
                            {{--לִיצוֹרמצב החינוך--}}
                        {{--</button>--}}
                        {{--<div class="col-sm-12">--}}
                            {{--<select class="chosen-select" id="stageOfEducationList" name="stage_of_education_id">--}}
                                {{--<option></option>--}}
                                {{--@foreach($stageOfEducations as $stageOfEducation)--}}
                                    {{--@if(old('stage_of_education_id') == $stageOfEducation->id)--}}
                                        {{--<option selected value="{{ $stageOfEducation->id }}">{{ $stageOfEducation->name }}</option>--}}
                                    {{--@else--}}
                                        {{--@if($job->stage_of_education_id == $stageOfEducation->id)--}}
                                        {{--<option selected value="{{ $stageOfEducation->id }}">{{ $stageOfEducation->name }}</option>--}}
                                        {{--@else--}}
                                        {{--<option value="{{ $stageOfEducation->id }}">{{ $stageOfEducation->name }}</option>--}}
                                        {{--@endif--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="control-label">שם עובד משאבי אנוש אחר:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="other_hr_name" value="{{ old('other_hr_name') ? old('other_hr_name') : $job->other_hr_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון משאבי אנוש אחר:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="other_hr_phone" value="{{ old('other_hr_phone') ? old('other_hr_phone') : $job->other_hr_phone }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון משאבי אנוש אחר:</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" multiple name="images[]">
                            <div class="row">
                            @foreach($job->images as $image)
                                <div class="col-md-4">
                                    <a href="#" style="font-size: 20px;color: red;" data-url="{{ route('store.job.files.remove', $image->id) }}" onclick="removeImage(this)"><span><i class="glyph-icon icon-remove"></i></span></a>
                                    <img class="img-responsive" src="{{ '/storage/jobs/'.$image->file }}">
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">פָּעִיל:</label>
                        <div class="col-sm-12">
                            <input type="checkbox" value="1" {{ old('active') ? 'checked' : ($job->active ? 'checked' : '') }} name="active">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">בָּדוּק:</label>
                        <div class="col-sm-12">
                            <input type="checkbox" value="1" {{ old('checked') ? 'checked' : ($job->checked ? 'checked' : '') }} name="checked">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">בשָׁנָה:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" value="{{ old('year') ? old('year') : ($job->year ? $job->year : '') }}" name="year">
                            <select multiple="multiple" name="type_of_year[]" class="form-control chosen-select">
                                <option></option>
                                @foreach($type_of_years as $year)
                                    @if(old('type_of_year') && in_array($year->id,old('type_of_year')))
                                        <option selected value="{{$year->id}}">{{ $year->name }}</option>
                                    @else
                                        @if($job->type_of_year()->count() && in_array($year->id,$job->type_of_year()->pluck('id')->toArray()))
                                        <option selected value="{{$year->id}}">{{ $year->name }}</option>
                                        @else
                                        <option value="{{$year->id}}">{{ $year->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">למי מיועד?</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" name="job_for">
                                <option></option>
                                @foreach($job_for_list as $list)
                                    @if(old('job_for') == $list)
                                        <option selected value="{{ $list }}">{{ $list }}</option>
                                    @else
                                        @if($job->job_for == $list)
                                        <option selected value="{{ $list }}">{{ $list }}</option>
                                        @else
                                        <option value="{{ $list }}">{{ $list }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">מסלול</label>
                        <div class="col-sm-12">
                            <select multiple="multiple" data-old="{{ json_encode(old('organization_route_ids')) }}"
                                    data-placeholder="Click to see" name="organization_route_ids[]"
                                    class="chosen-select">
                                @foreach(\App\OrganizationRoute::all() as $route)
                                    @if(old('organization_route_ids') && in_array($route->id,old('organization_route_ids')))
                                        @if($route->organization)
                                            <option selected value="{{ $route->id }}">{{ "(". $route->organization->name."): ".$route->name  }}</option>
                                        @else
                                            <option selected value="{{ $route->id }}">{{ $route->name  }}</option>
                                        @endif
                                    @else
                                        @if(!empty($job->organizationRoute()->get()->pluck('id')->toArray()) && in_array($route->id,$job->organizationRoute()->get()->pluck('id')->toArray()))
                                            @if($route->organization)
                                                <option selected value="{{ $route->id }}">{{ "(". $route->organization->name."): ".$route->name  }}</option>
                                            @else
                                                <option selected value="{{ $route->id }}">{{ $route->name  }}</option>
                                            @endif
                                        @else
                                            @if($route->organization)
                                                <option value="{{ $route->id }}">{{ "(". $route->organization->name."): ".$route->name  }}</option>
                                            @else
                                                <option value="{{ $route->id }}">{{ $route->name  }}</option>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Date:</label>
                        <div class="col-sm-12">
                            <input type="text" name="last_date_for_registration" value="{{ old('last_date_for_registration') ? old('last_date_for_registration') : ($job->last_date_for_registration ? $job->last_date_for_registration : '09/30/'.date('Y')) }}" class="datepicker last_date_for_registration" data-date-format="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Admin confirm job:</label>
                        <div class="col-sm-12">
                            <select name="admin_confirm" class="form-control">
                                <option value="0" @if($job->admin_confirm == 0) selected @endif>NO</option>
                                <option value="1" @if($job->admin_confirm == 1) selected @endif>YES</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">ערוך עבודה</button>
                    </div>
                </form>
            </div>

        </div>

        <script type="text/javascript" src="/js/jobs.js"></script>
@endsection
