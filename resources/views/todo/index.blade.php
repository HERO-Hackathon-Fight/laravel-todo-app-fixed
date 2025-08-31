@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Todo リスト') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

             

                    <!-- yuriko機能: フィルター・新規登録ボタン -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="filter-buttons">
                            <a href="{{ route('todo.index') }}" 
                               class="btn {{ request('done') === null ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                                <i class="fas fa-list me-1"></i>全てのタスク
                            </a>
                            <a href="{{ route('todo.index', ['done' => false]) }}" 
                               class="btn {{ request('done') === '0' || request('done') === 'false' ? 'btn-warning' : 'btn-outline-warning' }} me-2">
                                <i class="fas fa-clock me-1"></i>未完了
                            </a>
                            <a href="{{ route('todo.index', ['done' => true]) }}" 
                               class="btn {{ request('done') === '1' || request('done') === 'true' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-check me-1"></i>完了済み
                            </a>
                        </div>
                        
                        <div class="action-buttons">
                            @if(Route::has('todo.create'))
                                <a href="{{ route('todo.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-circle-plus pe-2"></i>
                                    <strong>新規登録</strong>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- tomoya機能: Todo追加フォーム（既存システム用） -->
                    @if(!Route::has('todo.create'))
                        <div class="todo-form mb-4">
                            <form action="{{ route('todo.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="new_title" class="form-label">
                                        <strong>新しいタスク</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           id="new_title"
                                           class="form-control" 
                                           placeholder="新しいタスクを入力..." 
                                           required 
                                           maxlength="255">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="new_due_date" class="form-label">
                                        <strong>期限日</strong>（任意）
                                    </label>
                                    <input type="date" 
                                           name="due_date" 
                                           id="new_due_date"
                                           class="form-control">
                                    <small class="form-text text-muted">
                                        期限を設定しない場合は空白のままにしてください。
                                    </small>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> タスクを追加
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- 統合されたTodo一覧表示 -->
                    <div class="todo-list">
                        @php
                            // フィルター処理
                            $filteredTodos = $todos ?? $todoItems ?? collect();
                            
                            if (request('done') === 'true' || request('done') === '1') {
                                $filteredTodos = $filteredTodos->filter(function($todo) {
                                    return $todo && isset($todo->completed) && $todo->completed;
                                });
                            } elseif (request('done') === 'false' || request('done') === '0') {
                                $filteredTodos = $filteredTodos->filter(function($todo) {
                                    return $todo && isset($todo->completed) && !$todo->completed;
                                });
                            }
                        @endphp

                        @forelse($filteredTodos as $todo)
                            {{-- Null チェックと必須プロパティの存在確認 --}}
                            @if($todo && isset($todo->id) && isset($todo->title))
                                <div class="todo-item {{ (isset($todo->completed) && $todo->completed) ? 'completed' : '' }} mb-3">
                                    <div class="card">
                                        @if(isset($todo->created_at))
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <span class="badge {{ (isset($todo->completed) && $todo->completed) ? 'bg-success' : 'bg-warning' }}">
                                                    {{ (isset($todo->completed) && $todo->completed) ? '完了' : '未完了' }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($todo->created_at)->timezone('Asia/Tokyo')->format('Y/m/d H:i:s') }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="card-body">
                                            <div class="todo-content d-flex justify-content-between align-items-center">
                                                <div class="todo-text-section flex-grow-1">
                                                    <span class="todo-text fs-5">{{ htmlspecialchars($todo->title) }}</span>
                                                    @if(isset($todo->due_date) && $todo->due_date)
                                                        <div class="text-muted mt-1" style="font-size: 0.9em;">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            期限: {{ \Carbon\Carbon::parse($todo->due_date)->format('Y/m/d') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="todo-actions d-flex gap-2">
                                                    <!-- yuriko機能: 編集ボタン（ルートが存在する場合） -->
                                                    @if(Route::has('todo.edit'))
                                                        <a href="{{ route('todo.edit', $todo->id) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="fa-solid fa-pen me-1"></i>編集
                                                        </a>
                                                    @endif
                                                    
                                                    <!-- tomoya機能: 完了/未完了切り替え -->
                                                    @if(Route::has('todo.toggle'))
                                                        <form action="{{ route('todo.toggle', $todo->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                @if(isset($todo->completed) && $todo->completed)
                                                                    <i class="fas fa-undo me-1"></i>戻す
                                                                @else
                                                                    <i class="fas fa-check me-1"></i>完了
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <!-- 削除ボタン -->
                                                    @if(Route::has('todo.destroy'))
                                                        <form action="{{ route('todo.destroy', $todo->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('このタスクを削除しますか？')">
                                                                <i class="fas fa-trash me-1"></i>削除
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted fs-5">
                                    @if(request('done') === 'true' || request('done') === '1')
                                        完了したタスクがありません。
                                    @elseif(request('done') === 'false' || request('done') === '0')
                                        未完了のタスクがありません。
                                    @else
                                        タスクがありません。新しいタスクを追加してください。
                                    @endif
                                </p>
                                @if(!Route::has('todo.create'))
                                    <p class="text-muted">上のフォームからタスクを追加できます。</p>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
