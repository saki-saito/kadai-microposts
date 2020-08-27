<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    public function index(){
        
        // ユーザー一覧をidの降順で取得
        $users = User::orderBy('id', 'desc')->paginate(10);
        
        // ユーザー一覧ビューで表示
        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    public function show($id){
        // idの値でユーザーを検索して取得
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        // ユーザーの投稿一覧を作成日時の降順で取得
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
        
        // ユーザー詳細ビューで表示
        return view('users.show', [
            'user' => $user,
            'microposts' => $microposts,
        ]);
    }
    
    /* ---------------------------------- *
     * ユーザーのフォロー一覧ページを表示するアクション
     * 
     * @param   $id ユーザーのid
     * @return  \Illuminate\Http\respose
     * ---------------------------------- */
    public function followings($id){
        
        // idの値でユーザーを検索して取得
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        // ユーザーのフォロー一覧を取得
        $followings = $user->followings()->paginate(10);
        
        // フォロー一覧ビューで表示
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
        ]);
    }
    
    /* ---------------------------------- *
     * ユーザーのフォロワー一覧ページを表示するアクション
     * 
     * @param   $id ユーザーのid
     * @return  \Illuminate\Http\Response
     * ---------------------------------- */
    public function followers($id){
        
        // idの値でユーザーを検索して取得
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        // ユーザーのフォロワー一覧を取得
        $followers = $user->followers()->paginate(10);
        
        // フォロワー一覧ビューで表示
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
    }
    
    /* ---------------------------------- *
     * idで指定されたユーザーのお気に入り一覧ページを表示するアクション
     * 
     * @param   $id ユーザーのid
     * @return  \Illuminate\Http\Response
     * ---------------------------------- */
    public function favorites($id){
        
        // idの値でユーザーを検索して取得
        $user = User::findOrFail($id);
        
        // 関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        // ユーザーのお気に入り一覧を取得
        $favorites = $user->favorites()->paginate(10);
        
        // お気に入り一覧ビューで表示
        return view('users.favorites', [
            'user' => $user,
            'microposts' => $favorites,
        ]);
    }
}
