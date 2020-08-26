<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/* ↓Authでも動く */
// use \Auth;
// use Illuminate\Support\Facades\Auth;

class MicropostsController extends Controller
{
    /* ------------------------------------------------- *
     * ユーザーの投稿一覧を取得してwelcomeビューへ
     * ------------------------------------------------- */
    public function index(){
        
        // 初期化
        $data = [];
        
        // 認証済のとき
        if (\Auth::check()){
            
            // 認証済ユーザーを取得
            $user = \Auth::user();
            
            // ユーザーの投稿の一覧を作成日時の降順で取得
            $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
        }
        
        // welcomeビューで表示
        return view('welcome', $data);
    }
    
    /* ------------------------------------------------- *
     * 新たに投稿をstoreして前のURLへ
     * ------------------------------------------------- */
    public function store(Request $request){
        
        // バリデーション
        $request->validate([
            'content' => 'required|max:255',
        ]);
        
        // 認証済ユーザーの投稿として作成
        $request->user()->microposts()->create([
            'content' => $request->content,
        ]);
        
        // 前のURLへリダイレクトさせる
        return back();
        
    }
    
    /* ------------------------------------------------- *
     * 投稿を削除して前のURLへ
     * ------------------------------------------------- */
    public function destroy($id){
        
        // idの値で投稿を検索して取得
        $micropost = \App\Micropost::findOrFail($id);
        
        // 認証済ユーザーがその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $micropost->user_id){
            $micropost->delete();
        }
        
        // 前のURLへリダイレクトさせる
        return back();
        
    }
}
