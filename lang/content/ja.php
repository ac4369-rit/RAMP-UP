<?php
// Japanese names for project content that comes from the database
// (resources, tasks, decisions, project info, timeline, change log, team rules).
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

    // Project and sprint
    'Invoices' => '請求書',
    'Customers can download every invoice as a PDF.' => 'お客様がすべての請求書を PDF でダウンロードできる。',
    'A web portal where customers can see their orders, download invoices and contact support in one place.' => 'お客様が注文の確認、請求書のダウンロード、サポートへの問い合わせを一か所でできるウェブポータル。',
    'Existing customers of a mid-sized online shop and the support team that answers their questions.' => '中規模オンラインショップの既存のお客様と、その質問に答えるサポートチーム。',
    'Login and order history are finished. The invoice page is in progress and support chat starts next sprint.' => 'ログインと注文履歴は完成。請求書ページは作業中で、サポートチャットは次のスプリントから始まる。',

    // Milestones
    'Login and accounts' => 'ログインとアカウント',
    'Order history' => '注文履歴',
    'Invoice page' => '請求書ページ',
    'Support chat' => 'サポートチャット',
    'Public launch' => '一般公開',

    // Change log
    'Invoices will be PDF only' => '請求書は PDF のみにする',
    'HTML invoices were dropped to save time.' => '時間短縮のため HTML の請求書はやめた。',
    'Jordan left the team' => 'Jordan がチームを離れた',
    'Jordan moved to another project. Alex takes over UX.' => 'Jordan は別のプロジェクトへ移り、Alex が UX を引き継ぐ。',
    'Support chat moved to next sprint' => 'サポートチャットは次のスプリントに移動',
    'The invoice page needed more time.' => '請求書ページにもっと時間が必要だった。',
    'Order history released to testers' => '注文履歴をテスターに公開',
    'First 20 customers can now see their orders.' => '最初の 20 人のお客様が注文を見られるようになった。',

    // Team rules
    'Meetings: Tuesday 18:00 on Discord' => 'ミーティング：毎週火曜 18:00、Discord にて',
    'Reply within 24 hours' => '24 時間以内に返信する',
    'Decisions go in the #decisions channel' => '決定事項は #decisions チャンネルに書く',
    'Make file names easy to find' => 'ファイル名は見つけやすくする',
];
