<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /* ----------------------------------------------------------- *
     * 一対多
     * ----------------------------------------------------------- *
     * このユーザーが所有する投稿（Microoistモデルのと関係を定義）
     * ----------------------------------------------------------- */
    public function microposts(){
        return $this->hasMany(Micropost::class);
    }
    
    /* ----------------------------------------------------------- *
     * このユーザーに関係するモデルの件数をロードする
     * ----------------------------------------------------------- */
    public function loadRelationshipCounts(){
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    /* ----------------------------------------------------------- *
     * 多対多
     * ----------------------------------------------------------- *
     * このユーザーがフォロー中のユーザー（Userモデルとの関係を定義）
     * ----------------------------------------------------------- */
    public function followings(){
        /* --------------------------------------------------------------------------------------------- *
         * belongsToMany()
         * 第一引数：相手先モデル
         * 第二引数：結合テーブル（中間テーブル）
         * 第三引数：指定した相手先モデルが参照に利用しているカラム名
         * 第四引数：指定した相手先モデル側がこの関数へ参照を持つために利用しているカラム名
         * --------------------------------------------------------------------------------------------- */
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    /* ----------------------------------------------------------- *
     * 多対多
     * ----------------------------------------------------------- *
     * このユーザーをフォロー中のユーザー（Userモデルとの関係を定義）
     * ----------------------------------------------------------- */
    public function followers(){
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /* ---------------------------------------------------- *
     * $userIdで指定されたユーザーをフォローする
     * 
     * @param   int $userId
     * @return  bool
     * ---------------------------------------------------- */
    public function follow($userId){
        
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かの確認
        $its_me = $this->id == $userId;
        
        // すでにフォローしている、または自分自身をフォローしようとしているとき
        if ($exist || $its_me){
            // 何もしない
            return false;
        }
        // 未フォローのとき
        else {
            // フォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /* ---------------------------------------------------- *
     * $userIdで指定されたユーザーをアンフォローする
     * 
     * @param   int $userId
     * @return  bool
     * ---------------------------------------------------- */
    public function unfollow($userId){
        
        // フォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かの確認
        $its_me = $this->id == $userId;
        
        // フォローしている、かつ相手が自分自身ではないとき
        if ($exist && !$its_me){
            // フォローをはずす
            $this->followings()->detach($userId);
            return true;
        }
        // フォローしていないとき
        else {
            // 何もしない
            return false;
            
        }
    }
    
    /* ------------------------------------------------------------------- *
     * 指定された$userIdのユーザーをこのユーザーがフォローしているか調べる
     * 
     * @param   int $userId
     * @return  bool    フォローしている：true
     *                  フォローしていない：false
     * ------------------------------------------------------------------- */
    public function is_following($userId){
        // フォロー中のユーザーのIDの中に$userIdが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /* ---------------------------------------------------------------------- *
     * このユーザーとフォロー中ユーザーの投稿に絞りこむ
     * ---------------------------------------------------------------------- */
    public function feed_microposts(){
        
        // このユーザーがフォロー中のユーザーidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザーのidも配列に追加
        $userIds[] = $this->id;
        
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    /* ----------------------------------------------------------- *
     * このユーザーがお気に入り登録中のツイート（Micropostモデルとの関係を定義）
     * ----------------------------------------------------------- */
    public function favorites(){
        /* --------------------------------------------------------------------------------------------- *
         * belongsToMany()
         * 第一引数：相手先モデル
         * 第二引数：結合テーブル（中間テーブル）
         * 第三引数：指定した相手先モデルが参照に利用しているこっちのカラム名
         * 第四引数：指定した相手先モデル側がこの関数へ参照を持つために利用しているあっちのカラム名
         * --------------------------------------------------------------------------------------------- */
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    /* ---------------------------------------------------- *
     * $postIdで指定されたツイートをお気に入り登録する
     * 
     * @param   int $postId
     * @return  bool
     * ---------------------------------------------------- */
    public function favorite($postId){
        
        // すでにお気に入り登録しているかの確認
        $exist = $this->is_favorite($postId);
        
        // すでにお気に入り登録しているとき
        if ($exist){
            // 何もしない
            return false;
        }
        // まだのとき
        else {
            // フォローする
            $this->favorites()->attach($postId);
            return true;
        }
    }
    
    /* ---------------------------------------------------- *
     * $postIdで指定されたツイートをお気に入り登録から削除する
     * 
     * @param   int $postId
     * @return  bool
     * ---------------------------------------------------- */
    public function unfavorite($postId){
        
        // お気に入り登録しているかの確認
        $exist = $this->is_favorite($postId);
        
        // お気に入り登録していないとき
        if (!$exist){
            // 何もしない
            return false;
        }
        // お気に入り登録しているとき
        else {
            // お気に入り登録削除する
            $this->favorites()->detach($postId);
            return true;
        }
    }
    
    /* ------------------------------------------------------------------- *
     * 指定された$postIdのツイートをこのユーザーがお気に入り登録しているか調べる
     * 
     * @param   int $postId
     * @return  bool    登録している：true
     *                  登録していない：false
     * ------------------------------------------------------------------- */
    public function is_favorite($postId){
        // お気に入り登録中のツイートのIDの中に$postIdが存在するか
        return $this->favorites()->where('micropost_id', $postId)->exists();
    }
}
