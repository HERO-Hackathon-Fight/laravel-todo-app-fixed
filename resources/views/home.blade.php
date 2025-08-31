@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Theme switcher -->
<div class="theme-switcher mb-3">
    <button class="btn btn-outline-secondary active" data-theme-btn="basic">
        ベーシック
    </button>
    <button class="btn btn-outline-primary" data-theme-btn="blue">
        ブルー
    </button>
    <button class="btn btn-outline-success" data-theme-btn="green">
        グリーン
    </button>
</div>

{{-- Todoコンテンツ部分も同様に追加 --}}
@endsection
