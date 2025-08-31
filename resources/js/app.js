import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Theme & Font System Starting...');
    
    // ========================================
    // テーマ＆フォント切り替え機能（強制適用版）
    // ========================================
    const themeSelect = document.getElementById('theme-select');
    const fontSelect = document.getElementById('font-select');
    const body = document.body;
    
    // 保存された設定を読み込み
    const savedTheme = localStorage.getItem('todo-theme') || 'basic';
    const savedFont = localStorage.getItem('todo-font') || 'standard';
    
    console.log('Saved theme:', savedTheme, 'Saved font:', savedFont);
    
    // 初期設定（data属性 + クラス名の両方で適用）
    function applyTheme(theme) {
        body.classList.remove('theme-basic', 'theme-blue', 'theme-green');
        body.setAttribute('data-theme', theme);
        body.classList.add(`theme-${theme}`);
        console.log('Theme applied:', theme);
    }
    
    function applyFont(font) {
        body.classList.remove('font-standard', 'font-child', 'font-young', 'font-senior');
        body.setAttribute('data-font', font);
        body.classList.add(`font-${font}`);
        console.log('Font applied:', font);
    }
    
    // 初期適用
    applyTheme(savedTheme);
    applyFont(savedFont);
    
    // セレクトボックスの初期値設定
    if (themeSelect) {
        themeSelect.value = `theme-${savedTheme}`;
    }
    if (fontSelect) {
        fontSelect.value = `font-${savedFont}`;
    }
    
    // ========================================
    // アクセシビリティサポート関数
    // ========================================
    
    // スクリーンリーダー用の音声通知
    function announceChange(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'visually-hidden';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        // 3秒後に削除
        setTimeout(() => {
            if (document.body.contains(announcement)) {
                document.body.removeChild(announcement);
            }
        }, 3000);
    }
    
    // ========================================
    // テーマ＆フォント変更イベント
    // ========================================
    
    // テーマ変更イベント
    if (themeSelect) {
        // 変更イベント
        themeSelect.addEventListener('change', function() {
            const theme = this.value.replace('theme-', '');
            applyTheme(theme);
            localStorage.setItem('todo-theme', theme);
            console.log('Theme changed to:', theme);
            
            // スクリーンリーダー用通知
            announceChange(`テーマを${this.options[this.selectedIndex].text}に変更しました`);
        });
        
        // キーボード操作強化
        themeSelect.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
            
            // 矢印キーでの即座変更（UX向上）
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                setTimeout(() => {
                    this.dispatchEvent(new Event('change'));
                }, 10);
            }
        });
    }
    
    // フォント変更イベント
    if (fontSelect) {
        // 変更イベント
        fontSelect.addEventListener('change', function() {
            const font = this.value.replace('font-', '');
            applyFont(font);
            localStorage.setItem('todo-font', font);
            console.log('Font changed to:', font);
            
            // スクリーンリーダー用通知
            announceChange(`フォントを${this.options[this.selectedIndex].text}に変更しました`);
        });
        
        // キーボード操作強化
        fontSelect.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
            
            // 矢印キーでの即座変更（UX向上）
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                setTimeout(() => {
                    this.dispatchEvent(new Event('change'));
                }, 10);
            }
        });
    }
    
    
    // ========================================
    // ショートカットキー機能
    // ========================================
    
    document.addEventListener('keydown', function(e) {
        // ショートカットキーが入力フィールド内で押された場合は無効にする
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.contentEditable === 'true') {
            return;
        }
        
        // Escキーでフォーカス解除
        if (e.key === 'Escape') {
            if (document.activeElement === themeSelect || document.activeElement === fontSelect) {
                document.activeElement.blur();
            }
        }
        
        // Alt+Tでテーマセレクトにフォーカス（Ctrl+Tはブラウザが使用）
        if (e.altKey && e.key === 't') {
            e.preventDefault();
            if (themeSelect) {
                themeSelect.focus();
                announceChange('テーマ選択にフォーカスしました');
                console.log('Shortcut: Alt+T - Theme selector focused');
            }
        }
        
        // Alt+Fでフォントセレクトにフォーカス（Ctrl+Fはブラウザが使用）
        if (e.altKey && e.key === 'f') {
            e.preventDefault();
            if (fontSelect) {
                fontSelect.focus();
                announceChange('フォント選択にフォーカスしました');
                console.log('Shortcut: Alt+F - Font selector focused');
            }
        }
    });

    // ========================================
    // Todo機能（既存のまま保持）
    // ========================================
    
    const todoForm = document.querySelector('form[action*="todo.store"]');
    if (todoForm) {
        todoForm.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="title"]');
            if (!input.value.trim()) {
                e.preventDefault();
                alert('タスクを入力してください');
                return;
            }
            console.log('Todo form submitted:', input.value);
        });
    }
    
    const toggleButtons = document.querySelectorAll('form[action*="toggle"] button');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Toggle button clicked');
        });
    });
    
    const deleteButtons = document.querySelectorAll('form[action*="destroy"] button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('このタスクを削除しますか？')) {
                e.preventDefault();
            }
        });
    });
    
    // ページロード完了通知
    console.log('Theme & Font System fully loaded!');
});
