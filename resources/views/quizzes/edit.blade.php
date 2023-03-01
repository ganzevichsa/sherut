@extends('layouts.main')

@section('content')
    <style>
        .answer-single-block {
            border: 1px solid gray;
        }
    </style>
    <div id="page-title">
        <h2>Edit Quiz</h2>
    </div>

    <div class="panel">
        @if($errors->any())
            {!! implode('', $errors->all('<p style="color:red">:message</p>')) !!}
        @endif
        <div class="panel-body">
            <div class="row">
                <form action="{{ route('quizzes.new.update', $quiz->id) }}" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="form-group">
                        <label class="control-label">Question: </label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="question">{{ $quiz->question }}</textarea>
                        </div>
                    </div>

                    @php
                        $data = json_decode($quiz->answer);
                    @endphp



                    <div class="form-group answers-block">

                        
                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 1: </label>
                                    <textarea class="form-control" name="answer[one]">{{ $data[0]->answer_one->text??'' }}</textarea>
                                </div>

                                @php
                                    $ans_1 = [];
                                    foreach($data[0]->answer_one->materials as $data1){
                                        $ans_1[] = $data1->material;
                                    }
                                @endphp

                                <div class="form-group">
                                    <label class="control-label">Materials 1: </label>
                                    <select name="material[one][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}" @if(array_search($material->title, $ans_1) OR array_search($material->title, $ans_1) === 0) selected @endif>{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 2: </label>
                                    <textarea class="form-control" name="answer[two]">{{ $data[1]->answer_two->text??'' }}</textarea>
                                </div>

                                @php
                                    $ans_2 = [];

                                    if(isset($data[1])):
                                        foreach($data[1]->answer_two->materials as $data2){
                                            $ans_2[] = $data2->material;
                                        }
                                    endif;
                                @endphp

                                <div class="form-group">
                                    <label class="control-label">Materials 2: </label>
                                    <select name="material[two][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}" @if(array_search($material->title, $ans_2) OR array_search($material->title, $ans_2) === 0) selected @endif>{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 3: </label>
                                    <textarea class="form-control" name="answer[three]">{{ $data[2]->answer_three->text??'' }}</textarea>
                                </div>

                                @php
                                    $ans_3 = [];

                                    if(isset($data[2])):
                                        foreach($data[2]->answer_three->materials as $data3){
                                            $ans_3[] = $data3->material;
                                        }
                                    endif;
                                @endphp

                                <div class="form-group">
                                    <label class="control-label">Materials 3: </label>
                                    <select name="material[three][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}" @if(array_search($material->title, $ans_3) OR array_search($material->title, $ans_3) === 0) selected @endif>{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Answer 4: </label>
                                    <textarea class="form-control" name="answer[four]">{{ $data[3]->answer_four->text??'' }}</textarea>
                                </div>

                                @php
                                    $ans_4 = [];

                                    if(isset($data[3])):
                                        foreach($data[3]->answer_four->materials as $data4){
                                            $ans_4[] = $data4->material;
                                        }
                                    endif;
                                @endphp

                                <div class="form-group">
                                    <label class="control-label">Materials 4: </label>
                                    <select name="material[four][]" multiple="multiple" class="form-control">
                                        @foreach($materials as $material)
                                            <option value="{{ $material->title }}" @if(array_search($material->title, $ans_4) OR array_search($material->title, $ans_4) === 0) selected @endif>{{ $material->title }}-{{ \App\Subcategory::find($material->cat)->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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
