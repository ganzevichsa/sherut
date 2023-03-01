<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Quiz;
use App\QuizAnswer;
use App\User;
use App\Models\QuizMaterials;
use App\Category;
use App\Subcategory;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class QuizController extends Controller
{
    public function getQuiz(Request $request)
    {
        try {
            $result = Quiz::paginate(1);
            return response()->json(['result' => $result], 201, ['utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function setQuiz(Request $request)
    {
        Log::info($request);

        try {
            QuizAnswer::create([
                'user_id' => $request['user'],
                'quiz_id' => $request['quiz'],
                'answer' => $request['answer'],
            ]);
            return response()->json(['status' => 'OK'], 201);
        } catch (\Exception $ex) {
            Log::info($ex);
            return response()->json(['status' => 'ERROR'], 500);
        }
    }

    public function getQuizResult(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $quizs = QuizAnswer::where('user_id', $user->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->orderBy('id', 'asc')
            ->groupBy('quiz_id')
            ->get();

        if($user->id != 1382){
            $user->auth_start = 0;
        }
        $user->save();
        // подсчёт процентов способностей в вопросах
        // Запись их в один массив с индексом
        $materials = array();
        $DBmaterials = QuizMaterials::get();

        foreach ($DBmaterials as $val) {
            $sum = 0;
            // dd($val->title);
            $res = Quiz::where('answer', 'like', '%' . $val->title . '%')->select('answer')->get();

            foreach ($res as $key) {
                $sum += substr_count($key->answer, ':"' . $val->title . '"');
            }
            if ($sum != 0) {
                $_sum = 100 / $sum;
                $materials[$val->title] = number_format($_sum, 2, '.', '');
            }
        }

        $result_cats = array();
        $cats_value = array();
        $ct_cal = array();

        foreach (QuizMaterials::select('title')->get() as $cat_info) {
            $cats_value[$cat_info->title] = 0;
            if (!isset($ct_cal[$cat_info->cat]))
                $ct_cal[$cat_info->cat] = 0;
        }

        foreach ($quizs as $qz) {
            $q_id = (int)$qz['quiz_id'];

            $quiz = Quiz::find($q_id);

            if (!empty($quiz)) {

                $data = json_decode($quiz['answer'])[$qz['answer'] - 1];

                if($qz['answer'] == 1)
                    $q_name = 'answer_one';
                elseif($qz['answer'] == 2)
                    $q_name = 'answer_two';
                elseif($qz['answer'] == 3)
                    $q_name = 'answer_three';
                elseif($qz['answer'] == 4)
                    $q_name = 'answer_four';

                // dd($data->{$q_name}->materials);

                foreach ($data->{$q_name}->materials as $new_res) {
                    $material = QuizMaterials::where('title', $new_res->material)->first();
                    if (!$material) continue;

                    // dd($material);

                    $cats_value[$material->title] = $cats_value[$material->title] + $materials[$material->title];
                }
            }
        }


        $sub_cat_progress = array();

        foreach ($cats_value as $key_cv => $cv) {

            $ability = QuizMaterials::where('title', $key_cv)->first();

            // $cat категория способности
            $cat = Subcategory::where('abilities', 'like', '%"ability_id":"'.$ability->id.'"%')->first();
            // dd($cat->value);
            // Проверка, пройдет ли минимальный порог способности
            // $cv >= $cat->value
            if (isset($cat)) {
                $abilities = json_decode($cat->abilities);
                foreach($abilities as $ab){
                    if($ability->id != $ab->ability_id) continue;

                    // Проверяем есть ли в массиве категория способности
                    if (!isset($sub_cat_progress[$cat->id])) {
                        // Если нет, тогда добавляем с стандартным значением
                        $sub_cat_progress[$cat->id] = ($ab->ability_value == null) ? 0 : $ab->ability_value;
                    } else {
                        // Если есть, тогда добавляем
                        $sub_cat_progress[$cat->id] += ($ab->ability_value == null) ? 0 : $ab->ability_value;
                    }
                }
            }
        }

        $cat_progress = array();

        foreach ($sub_cat_progress as $key_scp => $scp) {

            $cat = Subcategory::where('id', $key_scp)->first();
            if($cat->value <= $scp)
                $cat_progress[] = $key_scp;
        }


        $result = Subcategory::whereIn('id', $cat_progress)->get();
   
        return response()->json(['data' => $result], 201, ['utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
