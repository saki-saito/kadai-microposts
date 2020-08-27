@if (count($microposts) > 0)
    
    <ul class="list-unstyled">
        @foreach ($microposts as $micropost)
            <li class="media mb-3">
                {{-- 投稿者のメールアドレスをもとにGravatarを取得して表示 --}}
                <img class="mr-2 rounded"src="{{ Gravatar::get($micropost->user->email, ['size' => 50]) }}" alt="">
                <div class="media-body">
                    <div>
                        {{-- 投稿者のユーザー詳細ページへのリンク --}}
                        {!! link_to_route('users.show', $micropost->user->name, ['user' => $micropost->user->id]) !!}
                        <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                    </div>
                    <div>
                        {{-- 投稿内容 --}}
                        <p class="mb-0">{!! nl2br(e($micropost->content)) !!}</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-1">
                            {{-- お気に入りボタン --}}
                            @if (Auth::user()->is_favorite($micropost->id))
                                {{-- お気に入り削除ボタンのフォーム --}}
                                {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
                                    {{-- ↓'Unfavorite' --}}
                                    {{-- {!! Form::submit('Unfavorite', ['class' => 'btn btn-success btn-sm']) !!} --}}
                                    {{-- ↓星アイコン --}}
                                    {!! Form::button('<i class="fas fa-star"></i>', ['class' => 'btn btn-sm', 'type' => 'submit']) !!}
                                {!! Form::close() !!}
                            @else
                                {{-- お気に入りボタンのフォーム --}}
                                {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
                                    {{-- ↓'Favorite' --}}
                                    {{-- {!! Form::submit('Favorite', ['class' => 'btn btn-light btn-sm']) !!} --}}
                                    {{-- ↓白抜き星アイコン --}}
                                    {!! Form::button('<i class="far fa-star"></i>', ['class' => 'btn btn-sm', 'type' => 'submit']) !!}
                                {!! Form::close() !!}
                            @endif
                        </div>
                        @if (Auth::id() == $micropost->user_id)
                            <div class="col-sm-1">
                            {{-- 投稿削除ボタンのフォーム --}}
                            {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                                {{-- ↓'Delete' --}}
                                {{-- {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!} --}}
                                {{-- ↓ゴミ箱アイコン --}}
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['class' => 'btn btn-sm', 'type' => 'submit']) !!}
                            {!! Form::close() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    
    {{-- ページネーションのリンク --}}
    {!! $microposts->links() !!}
    
@endif
