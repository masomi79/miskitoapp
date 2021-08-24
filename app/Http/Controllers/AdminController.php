<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Example;
use App\Models\MiskitoWord;
use App\Models\SpanishWord;
use App\Models\miqEspRelation;
use App\Models\miqExRelation;
use Auth;

class AdminController extends Controller
{
    //

    public function loginCheck(){
        
        if(Auth::check()){

            $user = Auth::user();
            $userId = $user->id;
            $userName = $user->name;
            $userPriviledge = $user->priviledge;
            $loginInfo = [
                'userId' => $userId,
                'userName' => $userName,
                'userPriviledge' => $userPriviledge
            ];
            return $loginInfo;
        }else{
            return null;
        }
    }

    public function getTotalNumber(){

        // return "umco";
        return MiskitoWord::count();
    }

    public function checkWordsLang(Request $request){
        $word = request('word');
        $word = '%' . $word . '%';
        $hit = '';
        $hit = MiskitoWord::where('miskitoWord', 'like', $word)
        ->select('id')
        ->get();

        if($hit->isEmpty()){
            $hit = SpanishWord::where('spanishWord', 'like', $word)
            ->select('id')
            ->get();

            if($hit->isEmpty()){
                return 'no se encuentra';
            }else{
                return 'español';
            }

        }else{
            return 'miskito';
        }
        

    }
    //単語のIDを受け取り結果を返す
    public function getSearchResultById(Request $request){
        $id = request('id');
        $word = request('word');
        $lang = request('lang');
        $type = '';
        
        if($lang == 'miskito'){
            //意味の一覧を取得
            $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $id)
                ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                ->orderBy('spanish_words.spanishWord')
                ->get();
            //単語のタイプを取得
            $getType = MiskitoWord::where('id', '=', $id)
                ->select('type')
                ->get();
            $type = $getType[0]['type'];
        }else{
            $meanings = miqEspRelation::where('miq_esp_relations.spanishWord', '=', $id)
                ->leftJoin('miskito_words',  'miq_esp_relations.miskitoWord', '=', 'miskito_words.id')
                ->select('miskito_words.id', 'miskito_words.miskitoWord as word')
                ->orderBy('miskito_words.miskitoWord')
                ->get();
        }

        $resultsSet = [
            "lang" => $lang,
            "words" => $word,
            "cword" => $word,
            "match" => $id,
            "meanings" => $meanings,
            "type" => $type
        ];
        return $resultsSet;
    }


    //キーワードを受け取り検索結果を返す
    public function getSearchResult(Request $request){

        //データの初期化
        $matchId = "";
        $meanings = "";
        $plainWord = request('word');
        $wordSet = request('words');
        $wordslang = request('lang');
        $type = '';


        $n = count($wordSet);
        $completeWord = '';
        for($i=0; $i < $n; $i++){
            $completeWord = $completeWord . $wordSet[$i] . ' ';
        }
        $completeWord = trim($completeWord, ' ');

        //miskito入力された言語はMiskito語
        if($wordslang == 'miskito'){
            //入力されたキーワードにマッチするIDとtypeを取得
            $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $completeWord )
            ->select('id')
            ->get();

            //完全一致する単語が存在しない
            if($isPerfectMatch->isempty()){
                //サジェストを作成する

            //完全一致する単語が存在する
            }else{
                //$type = "n";
                //IDで対応する訳語のリストを作る
                $matchId = $isPerfectMatch[0]['id'];
                $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                    ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                    ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                    ->orderBy('spanish_words.spanishWord')
                    ->get();
                    
                //単語のタイプを取得
                $getType = MiskitoWord::where('id', '=', $matchId)
                ->select('type')
                ->get();
                $type = $getType[0]['type'];
            }
        //入力された言語はスペイン語
        }else if($wordslang == 'español'){
            //入力されたキーワードにマッチするIDを取得
            $isPerfectMatch = SpanishWord::where('spanishWord', '=', $completeWord )
            ->select('id')
            ->get();

            //完全一致する単語が存在しない
            if($isPerfectMatch->isempty()){
                //サジェストを作成する

            //完全一致する単語が存在する場合意味の一覧を取得
            }else{
                $matchId = $isPerfectMatch[0]['id'];
                $meanings = miqEspRelation::where('miq_esp_relations.spanishWord', '=', $matchId)
                    ->leftJoin('miskito_words',  'miq_esp_relations.miskitoWord', '=', 'miskito_words.id')
                    ->select('miskito_words.id', 'miskito_words.miskitoWord as word')
                    ->orderBy('miskito_words.miskitoWord')
                    ->get();
            }

        //どちらにも該当なし
        }else{
        }

        $resultsSet = [
            "cword" => $completeWord,
            "lang" => $wordslang,
            "match" => $matchId,
            "meanings" => $meanings,
            "type" => $type,
            "words" => $wordSet

        ];
        return $resultsSet;
    }

    ////////////////////キーワードを受け取り提案語句の一覧を返す/////////////////////////
    public function getSuggestions(Request $request){

        $word = request('word');

        $suggestions = MiskitoWord::where('miskitoWord', 'like', '%' . $word . '%')
        ->where('miskitoWord', '!=', $word)
        ->select('id','miskitoWord')
        ->orderBy('miskitoWord')
        ->get();



        return $suggestions;
    }

    ////////////////////キーワードを受け取り関連語句と意味の一覧を返す////////////////////
    public function getRelatedTerms(Request $request){
        
        $relatedTermsSet = [];
        $keyWord = request('words');
        $i = 0;

        if(count($keyWord) > 1){
            
            foreach($keyWord as $k){


                if( substr($k, -1) === 'i'){
                    $k = substr($k, 0, -1);
                    $k = $k . 'aia';
                }

                //辞書にあるかどうかをチェック
                $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $k )
                ->select('id')
                ->get();


                //完全一致する単語が存在しない
                if($isPerfectMatch->isempty()){
                
                //完全一致する単語が存在する
                }else{

                    $matchId = $isPerfectMatch[0]['id'];
                    $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                        ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                        ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                        ->orderBy('spanish_words.spanishWord')
                        ->get();

                    $line = [
                        'index' => $i,
                        'id' => $matchId,
                        'word' => $k,
                        'meanings' => $meanings
                    ];
                    $relatedTermsSet[] = $line;
                    $i++;
                }
                
                //動詞の名詞形の場合、動詞の原型を関連語句に追加
                if(substr($k, -4) === 'anka'){
                    $k = substr($k, 0, -4);
                    $k = $k . 'aia';

                    //辞書にあるかどうかをチェック
                    $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $k )
                    ->select('id')
                    ->get();

                    //完全一致する単語が存在しない
                    if($isPerfectMatch->isempty()){

                    //完全一致する単語が存在する
                    }else{

                        $matchId = $isPerfectMatch[0]['id'];
                        $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                            ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                            ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                            ->orderBy('spanish_words.spanishWord')
                            ->get();

                        $line = [
                            'index' => $i,
                            'id' => $matchId,
                            'word' => $k,
                            'meanings' => $meanings
                        ];
                        $relatedTermsSet[] = $line;
                        $i++;
                    }
                }
                //動詞の場合、名詞形を追加する
                if(substr($k, -3) === 'aia'){
                    $k = substr($k, 0, -3);
                    $k = $k . 'anka';

                    //辞書にあるかどうかをチェック
                    $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $k )
                    ->select('id')
                    ->get();

                    //完全一致する単語が存在しない
                    if($isPerfectMatch->isempty()){

                    //完全一致する単語が存在する
                    }else{

                        $matchId = $isPerfectMatch[0]['id'];
                        $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                            ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                            ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                            ->orderBy('spanish_words.spanishWord')
                            ->get();

                        $line = [
                            'index' => $i,
                            'id' => $matchId,
                            'word' => $k,
                            'meanings' => $meanings
                        ];
                        $relatedTermsSet[] = $line;
                        $i++;
                    }
                }

            }
        }elseif(count($keyWord) == 1){

            $k = $keyWord[0];

            //動詞の名詞形の場合、動詞の原型を関連語句に追加
            if(substr($k, -4) === 'anka'){
                $k = substr($k, 0, -4);
                $k = $k . 'aia';

                //辞書にあるかどうかをチェック
                $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $k )
                ->select('id')
                ->get();

                //完全一致する単語が存在しない
                if($isPerfectMatch->isempty()){

                //完全一致する単語が存在する
                }else{

                    $matchId = $isPerfectMatch[0]['id'];
                    $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                        ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                        ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                        ->orderBy('spanish_words.spanishWord')
                        ->get();

                    $line = [
                        'index' => $i,
                        'id' => $matchId,
                        'word' => $k,
                        'meanings' => $meanings
                    ];
                    $relatedTermsSet[] = $line;
                    $i++;
                }
            }


            //動詞の場合、名詞形を追加する
            if(substr($k, -3) === 'aia'){
                $k = substr($k, 0, -3);
                $k = $k . 'anka';

                //辞書にあるかどうかをチェック
                $isPerfectMatch = MiskitoWord::where('miskitoWord', '=', $k )
                ->select('id')
                ->get();

                //完全一致する単語が存在しない
                if($isPerfectMatch->isempty()){

                //完全一致する単語が存在する
                }else{

                    $matchId = $isPerfectMatch[0]['id'];
                    $meanings = miqEspRelation::where('miq_esp_relations.miskitoWord', '=', $matchId)
                        ->leftJoin('spanish_words',  'miq_esp_relations.spanishWord', '=', 'spanish_words.id')
                        ->select('spanish_words.id', 'spanish_words.spanishWord as word')
                        ->orderBy('spanish_words.spanishWord')
                        ->get();

                    $line = [
                        'index' => $i,
                        'id' => $matchId,
                        'word' => $k,
                        'meanings' => $meanings
                    ];
                    $relatedTermsSet[] = $line;
                    $i++;
                }
            }
            
        }

        if(count($relatedTermsSet) > 0){
            return $relatedTermsSet;
        }else{
            return null;
        }
        
    }

    //単語の登録
    public function registerNewWord(){

        $this->validate(request(), [
        'newMiskito' => 'required'
        ]
        );


        if(Auth::check()){

        $user = Auth::user();

        $miskitoWord = MiskitoWord::firstOrNew(['miskitoWord' => request('newMiskito')]);
            $miskitoWord->user = $user->id;
            $miskitoWord->save();

        if(request('newSpanish')){
            $spanishWord = SpanishWord::firstOrNew(['spanishWord' => request('newSpanish')]);
            $spanishWord->save();
            $miqEspRelation = new miqEspRelation;
            $miqEspRelation->miskitoWord = $miskitoWord->id;
            $miqEspRelation->spanishWord = $spanishWord->id;
            $miqEspRelation->weight = '50';
            $miqEspRelation->user = $user->id;
            $miqEspRelation->save();
        }
        $message = 'Se ha registrado exitosamente. ¡gracias!';
        }else{
        $message = 'inicie session por favor!';
        }
        return $message;
    }

    //削除
    public function deleteRelation(Request $request){

        $resultsSet = [
            'message' => '',
            'spanish_word' => '',
            'miskito_word' => '',
        ];

        $user = Auth::user();
        if($user->priviledge > 0){
            $lang = request('lang');
            $word = request('word');

            if($lang == 'miskito'){
                $spanish_word = SpanishWord::where('spanishWord', '=', $word)->get('id');
                $spanish_word_id = $spanish_word[0]['id'];
                $miskito_word_id = request('opositId');
            }elseif($lang == 'español'){
                $miskito_word = MiskitoWord::where('miskitoWord', '=', $word)->get('id');
                $miskito_word_id = $miskito_word[0]['id'];
                $spanish_word_id = request('opositId');
            }

            if($spanish_word_id | $miskito_word_id){

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




            }else{
                $message = 'hoge';
            }          

            $resultsSet = [
                'message' => $message,
                'spanish_word' => $spanish_word_id,
                'miskito_word' => $miskito_word_id
            ];

        }else{
            $message = 'you are not priviledged.';

            $resultsSet = [
                'message' => $message,
                'spanish_word' => null,
                'miskito_word' => null
            ];
        }

        return $resultsSet;

    }

    //単語が辞書に存在するかのチェック
    public function wordCheck(Request $request){
        $check = MiskitoWord::where('miskitoWord', '=', request('word'))->exists();
        return $check;
    }

    //単語の種類の設定
    public function setWordType(){
        $mesasge = '';
        $id = request('id');
        $type = request('type');
        if(Auth::check()){
            $word = MiskitoWord::find($id);
            $word->type = $type;
            $word->save();
            $message = 'Se ha registrado exitosamente. ¡Gracias!';
        }else{
            $message = 'inicie session por favor!';
        }

    }

    //新規例文の登録
    public function registerNewExample(Request $request){
        $targetWord = MiskitoWord::where('miskitoWord', '=', request('targetWord'))->get('id');
        $targetWordId = $targetWord[0]['id'];

        $message = 'umco';

        $newExamples = Example::firstOrNew(['miskito_sentence' => request('newExampleMiq')]);
            $newExamples->spanish_sentence = request('newExampleEsp');
            $newExamples->save();
            $miqExRelation = new miqExRelation;
            $miqExRelation->miskito_sentence_id = $newExamples->id;
            $miqExRelation->miskito_word_id = $targetWordId;
            $miqExRelation->save();

        $resultsSet = [
            'miq' => request('newExampleMiq'),
            'esp' => request('newExampleEsp'),
            'target' => request('targetWord'),
            'message' => $message
        ];

        return $resultsSet;
    }

}
