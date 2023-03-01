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
                <form action="@if(isset($material)) {{ route('quiz.materials.update', $material) }} @else {{ route('quiz.materials.store') }} @endif" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">Ability name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="title" value="{{ $material->title??'' }}">
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label>Sub Category</label>
                        <div class="col-sm-12">
                            <select id="cat" class="js-single form-control" name="cat">
                                <option value="">-----------</option>
                                @foreach($subcategories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($material))? ($cat->id == $material->cat)? 'selected':'' :'' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Ability value:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="value" value="{{ $material->value??'' }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Passage point (percent): </label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="point" value="{{ $material->point??'' }}">
                        </div>
                    </div> -->
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">שמירה</button>
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
@stop