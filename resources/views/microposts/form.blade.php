{!! Form::open(['route' => 'microposts.store']) !!}
    <div class="form-group">
        {!! Form::textarea('content', old('content'), ['class' => 'form-control', 'rows' => '2']) !!}
        {{-- ↓'Post' --}}
        {!! Form::submit('Post', ['class' => 'btn btn-primary btn-block']) !!}
        {{-- ↓紙飛行機アイコン --}}
        {{-- {!! Form::button('<i class="fas fa-paper-plane"></i>', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!} --}}
    </div>
{!! Form::close() !!}