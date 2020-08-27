<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /* ------------------------------------------- *
     * idで指定されたツイートをお気に入りに登録する
     * 
     * @param   $id 登録するツイートのid
     * @return  \Illuminate\Http\Response
     * ------------------------------------------- */
    public function store($id){
        
        // 認証済ユーザー（閲覧者）がidのツイートをお気に入り登録する
        \Auth::user()->favorite($id);
        
        return back();
    }
    
    /* ------------------------------------------- *
     * idで指定されたツイートをお気に入りから削除する
     * 
     * @param   $id 削除するツイートのid
     * @return  \Illuminate\Http\Response
     * ------------------------------------------- */
    public function destroy($id){
        
        // 認証済ユーザー（閲覧者）がidのツイートのお気に入り登録を削除する
        \Auth::user()->unfavorite($id);
        
        return back();
    }
}
