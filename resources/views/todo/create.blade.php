@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="my-5 text-center header-green">タスク登録</h2>

        <div class="row">
            <div class="col-12 col-md-8 mx-auto">
                {{-- バリデーションエラー部分テンプレート --}}
                @include('layouts.errors')

                {{ Form::open(['url' => route('todo.store')]) }}

                <div class="my-3">
                    {{ Form::label('title', 'タイトル') }}
                    {{ Form::text('title', '', ['class' => 'form-control']) }}
                </div>

                <div class="my-3">
                    {{ Form::label('due_date', '期限') }}
                    {{ Form::date('due_date', '', ['class' => 'form-control']) }}
                </div>

                <div class="text-center">
                    {{ Form::submit('登録する', ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
