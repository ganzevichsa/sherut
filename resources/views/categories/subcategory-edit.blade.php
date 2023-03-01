@extends('layouts.main')

@section('content')
    <div id="page-title">
        @if(isset($material))
            <h2>Edit Quiz ability</h2>
        @else
            <h2>Create Quiz ability</h2>
        @endif
    </div>
    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="panel-body">
            <div class="row">
                <form method="POST" data-token="{{ csrf_token() }}" action="{{ route('subcategory.edit.update', $category) }}">
                    
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">צור / ערוך קטגוריית משנה</h4></div>
                    <div class="modal-body">

                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name" value="{{ $category->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריה:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="SubcategoryCategoryList" name="category_id">
                                <option></option>
                                @foreach(\App\Category::all() as $cat)
                                    <option value="{{ $cat->id }}" @if($category->category_id == $cat->id) selected @endif>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Video Url</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="video_url" value="{{ $category->video_url }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Images</label>
                        <div class="col-sm-12">
                            <div class="img-block" style="display: none;">

                            </div>
                            <input type="file" multiple class="form-control" name="images[]">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Text</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="text">{{ $category->text }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Min. count priority</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="value" value="{{ $category->value }}">
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label class="control-label">Priority</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="priority">
                        </div>
                    </div> -->
                    <div class="modal-footer">

                        @if($category->abilities)
                            @php
                                $abilities = json_decode($category->abilities);
                            @endphp

                            @foreach($abilities as $ability)
                                @include('categories.ability-edit', ['data' => $ability])
                            @endforeach
                        @else
                            @include('categories.ability')
                        @endif
                        
                        <div id="result-ability"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary add-ability">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="{{ route('categories.index') }}" class="btn btn-default" data-dismiss="modal">סגור</a>
                        <button type="submit" class="btn btn-primary">שמור שינויים</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script type="text/javascript" src="/js/jobs.js"></script>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .bg-white>a {
            color: #fff!important;
        }

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 10px) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }
    </style>
@stop


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $('.js-single').select2();
    </script>
    <script type="text/javascript">
        $(document).on('click', '.add-ability', function(){
            $.ajax({
                type: 'GET',
                url: '/subcategories/get/ability',
                success: function(result){
                    $('#result-ability').append(result.html);
                }
            });
        })

        $(document).on('click', '.delete-ability', function(){
            $(this).parents('.item-ability').remove();
        })
    </script>
@stop