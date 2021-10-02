<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Models\MiskitoWord;
use App\Models\SpanishWord;
use App\Models\Example;
use App\Models\miqEspRelation;
use App\Models\miqExRelation;


class DicController extends Controller
{
    //

    //単語を受け取り、検索候補を返す
    public function getSuggestionsFromWord(Request $request){


        $word = request('word');


        $miqs = MiskitoWord::where('miskitoWord', 'like', '%' . $word . '%')
            ->where('miskitoWord', '!=', $word)
            ->select('id','miskitoWord')
            ->orderBy('miskitoWord')
            ->get();

        $esps = SpanishWord::where('spanishWord', 'like', '%' . $word . '%')
            ->where('spanishWord', '!=', $word)
            ->select('id','spanishWord')
            ->orderBy('spanishWord')
            ->get();


        $suggestions = [
            'miq'=>$miqs,
            'esp'=>$esps
        ];

        return $suggestions;
    

    }

    //単語と言語からIDを取得
    public function getIdFromWord(Request $request){

        $word = request('word');
        $lang = request('lang');

        if($lang == 'miskito'){
            $rs = MiskitoWord::where('miskitoWord', '=', $word)->select('id')->get();
            if(count($rs) > 0){
                $id = $rs[0]['id'];
            }else{
                $id = 'no se encuentra';
            }
        }elseif($lang == 'español'){
            $rs = SpanishWord::where('spanishWord', '=', $word)->select('id')->get();
            if(count($rs) > 0){
                $id = $rs[0]['id'];
            }else{
                $id = 'no se encuentra';
            }
        }else{
            $id = 'no se encuentra';
        }

        return $id;
    }
    //IDと言語から単語を取得
    public function getWordFromId(Request $request){

        $id = request('id');
        $lang = request('lang');
        $type = "";

        if($lang == 'miq' || $lang == 'miskito'){
            $rc = MiskitoWord::where('id', '=', $id)->select('miskitoWord')->get();
            $word = $rc[0]['miskitoWord'];
            $getType = MiskitoWord::where('id', '=', $id)
                ->select('type')
                ->get();
            $type = $getType[0]['type'];
        }

        if($lang == 'esp' || $lang == 'español'){
            $rc = SpanishWord::where('id', '=', $id)->select('spanishWord')->get();
            $word = $rc[0]['spanishWord'];
        }

        $word = [
            "id" => $id,
            "word" => $word,
            "type" => $type
        ];

        return $word;
    }


    //単語のIDを受け取り例文を返す
    public function getExamples(Request $request){
        $word_id = request('id');

        $examples = miqExRelation::where('miskito_word_id', '=', $word_id)
            ->leftJoin('examples', 'miq_ex_relations.miskito_sentence_id', '=', 'examples.id')
            ->select('miskito_sentence', 'spanish_sentence')
            ->get();

        return $examples;
    }


    //単語のIDと言語から意味の一覧を返す
    public function getMeanings(Request $request){
        $lang = request('lang');
        $id = request('id');

        if($lang == 'miq' || $lang == 'miskito'){
            $result = miqEspRelation::where('miskitoWord', '=', $id)
            ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
            ->select('spanish_words.id', 'spanish_words.spanishWord as word')
            ->orderBy('spanish_words.spanishWord')
            ->get();
        }elseif($lang == 'esp' || $lang == 'español'){
            $result = miqEspRelation::where('spanishWord', '=', $id)
            ->leftJoin('miskito_words',  'miq_esp_relations.miskitoWord', '=', 'miskito_words.id')
            ->select('miskito_words.id', 'miskito_words.miskitoWord as word')
            ->orderBy('miskito_words.miskitoWord')
            ->get();
        }

        return $result;
    }

    //ブランクのrelationを削除する
    public function deleteRelation(Request $request){


        $user = Auth::user();
//        $user = $request->user();
//        $userp = $user->name;
//        if($userp > 0){
//
            /*
                $message = '';
                // 関係を削除
                $relationDeleted = miqEspRelation::where('miskitoWord', '=', $miskito_word_id)
                ->where('spanishWord', '=', $spanish_word_id)
                ->delete();

                //　それぞれの単語がmiqEspRelationになければその単語も削除
                $miqCheck = miqEspRelation::where('miskitoWord', '=', $miskito_word_id)->first();
                if(is_null($miqCheck)){
                    MiskitoWord::find($miskito_word_id)->delete();
                    $message .= 'miskito word' . $miskito_word_id . ' was deleted.';
                }else{
                    $message .= 'miskito word' . $miskito_word_id . ' still has relation.';
                }

            */
  //          $message = 'ok, Go!';


    //    }else{
      //      $message = 'no';
       // }
       $umc = "umco" . $user;

//        return $user;
        return $umc;
    }


}
