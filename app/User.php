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
        $this->loadCount(['microposts', 'followings', 'followers']);
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
    

}
