<?php
// Japanese names for project content that comes from the database
// (link texts: resources, tasks and decisions).
// Key = the English text stored in the database, value = what is shown.
// Machine-assisted: must be checked by a speaker.
return [
    // Resources
    'Design onboarding guide'  => 'デザインのオンボーディングガイド',
    'Figma file: Atlas'        => 'Figma ファイル：Atlas',
    'Old style guide (v1)'     => '旧スタイルガイド（v1）',
    'Accessibility checklist'  => 'アクセシビリティチェックリスト',
    'Backend setup guide'      => 'バックエンドのセットアップガイド',
    'Code repository'          => 'コードリポジトリ',
    'Component list'           => 'コンポーネント一覧',

    // Tasks
    'Review the invoice page wireframe'   => '請求書ページのワイヤーフレームをレビューする',
    'Fix contrast on the login button'    => 'ログインボタンのコントラストを修正する',
    'Build invoice download'              => '請求書のダウンロード機能を作る',
    'Test order history on mobile'        => 'スマホで注文履歴をテストする',
    'Set up database'                     => 'データベースを準備する',
    'Design the empty order history'      => '空の注文履歴をデザインする',
    'Redesign the invoice download button' => '請求書ダウンロードボタンを再デザインする',

    // Decisions
    'Use PHP and MySQL for the backend'              => 'バックエンドには PHP と MySQL を使う',
    'Invoices are generated as PDF only'             => '請求書は PDF のみで作成する',
    'Mobile-first layout for every screen'           => 'すべての画面をモバイルファーストで作る',
    'Login with email and password, no social login' => 'メールとパスワードでログイン（SNS ログインなし）',
];
