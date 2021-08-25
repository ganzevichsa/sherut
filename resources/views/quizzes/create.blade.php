@extends('layouts.main')

@section('content')
    <style>
        .answer-single-block {
            border: 1px solid gray;
        }
    </style>
    <div id="page-title">
        <h2>Create Quiz</h2>
    </div>

    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('quizzes.store') }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="form-group">
                        <label class="control-label">Question: </label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="question">{{ old('question') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group answers-block">

                        
                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 1: </label>
                                    <textarea class="form-control" name="answer[one]"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Materials 1: </label>
                                    <select name="material[one][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}">{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 2: </label>
                                    <textarea class="form-control" name="answer[two]"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Materials 2: </label>
                                    <select name="material[two][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}">{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 3: </label>
                                    <textarea class="form-control" name="answer[three]"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Materials 3: </label>
                                    <select name="material[three][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}">{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 4: </label>
                                    <textarea class="form-control" name="answer[four]"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Materials 4: </label>
                                    <select name="material[four][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}">{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">שמור</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/quiz.js"></script>

@endsection
