@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">タスク編集</h5>
                    <a href="{{ route('todo.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>戻る
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('todo.update', $todo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">
                                <strong>タスク名</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $todo->title) }}" 
                                   placeholder="タスク名を入力してください..." 
                                   required 
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 期限日フィールド（常に表示するように修正） --}}
                        <div class="form-group mb-3">
                            <label for="due_date" class="form-label">
                                <strong>期限日</strong>
                            </label>
                            <input type="date" 
                                   name="due_date" 
                                   id="due_date"
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   value="{{ old('due_date', isset($todo->due_date) && $todo->due_date ? \Carbon\Carbon::parse($todo->due_date)->format('Y-m-d') : '') }}">
                            <small class="form-text text-muted">
                                期限を設定しない場合は空白のままにしてください。
                            </small>
                            @error('due_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">
                                <strong>ステータス</strong>
                            </label>
                            <div>
                                <span class="badge {{ (isset($todo->completed) && $todo->completed) ? 'bg-success' : 'bg-warning' }} fs-6">
                                    {{ (isset($todo->completed) && $todo->completed) ? '完了' : '未完了' }}
                                </span>
                                <small class="form-text text-muted d-block mt-1">
                                    ステータスの変更は一覧画面から行ってください。
                                </small>
                            </div>
                        </div>

                        @if(isset($todo->created_at))
                            <div class="form-group mb-4">
                                <label class="form-label">
                                    <strong>作成日時</strong>
                                </label>
                                <div class="form-control-plaintext">
                                    {{ \Carbon\Carbon::parse($todo->created_at)->timezone('Asia/Tokyo')->format('Y年m月d日 H:i:s') }}
                                </div>
                            </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('todo.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>キャンセル
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>更新する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
