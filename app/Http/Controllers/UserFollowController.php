<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    /* ---------------------------------- *
     * ユーザーをフォローする
     * 
     * @rparam  $id 相手のユーザーid
     * @return  \Illuminate\Http\Response
     * ---------------------------------- */
    public function store($id){
        // 認証済ユーザー（閲覧者）がidのユーザーをフォローする
        \Auth::user()->follow($id);
        // 前のURLへリダイレクトさせる
        return back();
    }
    
    /* ---------------------------------- *
     * ユーザーをアンフォローする
     * 
     * @param   $id 相手のユーザーid
     * @return  \Illuminate\Http\Response
     * ---------------------------------- */
    public function destroy($id){
        // 認証済ユーザー（閲覧者）がidのユーザーをアンフォローする
        \Auth::user()->unfollow($id);
        // 前のURLへリダイレクトさせる
        return back();
    }
}
