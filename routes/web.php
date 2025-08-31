<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Todo関連のルート（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/todo', function () {
        $todos = session('todos', collect());
        
        if ($todos->isEmpty()) {
            $todos = collect([
                (object)[
                    'id' => 1, 
                    'title' => 'サンプルタスク1', 
                    'completed' => false,
                    'created_at' => now(),
                    'due_date' => now()->addDays(7)
                ],
                (object)[
                    'id' => 2, 
                    'title' => 'サンプルタスク2', 
                    'completed' => true,
                    'created_at' => now()->subDays(1),
                    'due_date' => null
                ],
                (object)[
                    'id' => 3, 
                    'title' => 'サンプルタスク3', 
                    'completed' => false,
                    'created_at' => now()->subHours(2),
                    'due_date' => now()->addDays(3)
                ],
            ]);
            session(['todos' => $todos]);
        }
        
        $todos = $todos->filter(function($todo) {
            return $todo !== null && isset($todo->id) && isset($todo->title);
        })->values();
        
        $todoItems = $todos;
        
        return view('todo.index', compact('todos', 'todoItems'));
    })->name('todo.index');

    Route::post('/todo', function (Request $request) {
        $request->validate([
            'title' => 'required|max:255|string',
            'due_date' => 'nullable|date'
        ]);
        
        $todos = session('todos', collect());
        $todos = $todos->filter(function($todo) {
            return $todo !== null && is_object($todo) && isset($todo->id);
        });
        
        $maxId = 0;
        foreach ($todos as $todo) {
            if (isset($todo->id) && is_numeric($todo->id)) {
                $maxId = max($maxId, (int)$todo->id);
            }
        }
        $newId = $maxId + 1;
        
        $newTodo = new \stdClass();
        $newTodo->id = $newId;
        $newTodo->title = trim($request->title);
        $newTodo->completed = false;
        $newTodo->created_at = now();
        $newTodo->due_date = $request->due_date && !empty(trim($request->due_date)) 
            ? $request->due_date 
            : null;
        
        $todos->push($newTodo);
        session(['todos' => $todos]);
        
        $message = 'タスク「' . $newTodo->title . '」が追加されました';
        if ($newTodo->due_date) {
            $dueDate = \Carbon\Carbon::parse($newTodo->due_date)->format('Y/m/d');
            $message .= '（期限: ' . $dueDate . '）';
        }
        
        return redirect()->route('todo.index')->with('success', $message);
    })->name('todo.store');

    Route::patch('/todo/{id}/toggle', function ($id) {
        $todos = session('todos', collect());
        
        $foundTodo = null;
        $todos = $todos->map(function ($todo) use ($id, &$foundTodo) {
            if ($todo !== null && isset($todo->id) && $todo->id == $id) {
                if (!isset($todo->completed)) {
                    $todo->completed = false;
                }
                $todo->completed = !$todo->completed;
                $foundTodo = $todo;
            }
            return $todo;
        });
        
        session(['todos' => $todos]);
        
        if ($foundTodo) {
            $status = $foundTodo->completed ? '完了' : '未完了';
            return redirect()->route('todo.index')->with('success', 'タスク「' . $foundTodo->title . '」を' . $status . 'にしました');
        } else {
            return redirect()->route('todo.index')->with('error', 'タスクが見つかりませんでした');
        }
    })->name('todo.toggle');

    Route::delete('/todo/{id}', function ($id) {
        $todos = session('todos', collect());
        
        $deletedTodo = null;
        foreach ($todos as $todo) {
            if ($todo !== null && isset($todo->id) && $todo->id == $id) {
                $deletedTodo = $todo;
                break;
            }
        }
        
        $todos = $todos->filter(function ($todo) use ($id) {
            return $todo !== null && isset($todo->id) && $todo->id != $id;
        });
        
        $todos = $todos->values();
        session(['todos' => $todos]);
        
        if ($deletedTodo) {
            return redirect()->route('todo.index')->with('success', 'タスク「' . $deletedTodo->title . '」が削除されました');
        } else {
            return redirect()->route('todo.index')->with('error', 'タスクが見つかりませんでした');
        }
    })->name('todo.destroy');

    Route::get('/todo/{id}/edit', function ($id) {
        $todos = session('todos', collect());
        $todo = $todos->firstWhere('id', $id);
        
        if (!$todo) {
            return redirect()->route('todo.index')->with('error', 'タスクが見つかりません');
        }
        
        return view('todo.edit', compact('todo'));
    })->name('todo.edit');

    Route::put('/todo/{id}', function ($id, Request $request) {
        $request->validate([
            'title' => 'required|max:255|string',
            'due_date' => 'nullable|date'
        ]);
        
        $todos = session('todos', collect());
        
        $foundTodo = null;
        $todos = $todos->map(function ($todo) use ($id, $request, &$foundTodo) {
            if ($todo !== null && isset($todo->id) && $todo->id == $id) {
                $todo->title = trim($request->title);
                $todo->due_date = $request->due_date && !empty(trim($request->due_date)) 
                    ? $request->due_date 
                    : null;
                $todo->updated_at = now();
                $foundTodo = $todo;
            }
            return $todo;
        });
        
        session(['todos' => $todos]);
        
        if ($foundTodo) {
            $message = 'タスク「' . $foundTodo->title . '」を更新しました';
            if ($foundTodo->due_date) {
                $dueDate = \Carbon\Carbon::parse($foundTodo->due_date)->format('Y/m/d');
                $message .= '（期限: ' . $dueDate . '）';
            }
            return redirect()->route('todo.index')->with('success', $message);
        } else {
            return redirect()->route('todo.index')->with('error', 'タスクが見つかりませんでした');
        }
    })->name('todo.update');
});
