<?php
// Japanese text for project content that comes from the database
// (titles, descriptions, notes, messages, glossary, decisions, team info).
// Key = the English text exactly as stored in the database, value = what is shown.
// Text that is not in this list is shown as it is stored (for example new posts people write).
// Used by tc() in src/helpers.php.
// Machine-assisted: should be checked by a Japanese speaker.
return [

    // Resources
    'Design onboarding guide' => 'デザインのオンボーディングガイド',
    'Figma file: Atlas' => 'Figma ファイル：Atlas',
    'Old style guide (v1)' => '旧スタイルガイド（v1）',
    'Accessibility checklist' => 'アクセシビリティチェックリスト',
    'Backend setup guide' => 'バックエンドのセットアップガイド',
    'Code repository' => 'コードリポジトリ',
    'Component list' => 'コンポーネント一覧',

    // Tasks: titles
    'Review the invoice page wireframe' => '請求書ページのワイヤーフレームをレビューする',
    'Fix contrast on the login button' => 'ログインボタンのコントラストを修正する',
    'Build invoice download' => '請求書のダウンロード機能を作る',
    'Test order history on mobile' => 'スマホで注文履歴をテストする',
    'Set up database' => 'データベースを準備する',
    'Design the empty order history' => '空の注文履歴をデザインする',
    'Redesign the invoice download button' => '請求書ダウンロードボタンを再デザインする',

    // Tasks: descriptions
    'Leave comments on the invoice page design in Figma.' => 'Figma で請求書ページのデザインにコメントを残す。',
    'The button text fails the 4.5:1 contrast check.' => 'ボタンの文字がコントラスト 4.5:1 の基準を満たしていない。',
    'Customers can download a PDF of each invoice.' => 'お客様が各請求書の PDF をダウンロードできる。',
    'Check the page at 320px width and 200% zoom.' => '幅 320px と 200% ズームでページを確認する。',
    'Create tables and demo data.' => 'テーブルとデモデータを作る。',
    'Customers with no orders see a blank page. Design a friendly empty state.' => '注文がないお客様には空白のページが表示される。わかりやすい空の状態をデザインする。',
    'Make the download button match the new button style.' => 'ダウンロードボタンを新しいボタンのスタイルに合わせる。',

    // Tasks: notes, progress and feedback
    "Previous work: I planned the page structure and marked the open questions in Figma (page \"Invoice\", red comments).\nWhat you need to do: go through the wireframe, answer my comments and add your own.\nImportant: keep it consistent with the order history page. Maya decides on anything about the PDF layout." => "これまでの作業：ページの構成を考え、Figma（ページ「Invoice」、赤いコメント）に未解決の質問を記入しました。\nやってほしいこと：ワイヤーフレームを確認し、私のコメントに答えて、自分のコメントも追加してください。\n重要：注文履歴ページとそろえてください。PDF のレイアウトに関することは Maya が決めます。",
    'PDF generation works, download button still missing.' => 'PDF の作成は動いているが、ダウンロードボタンがまだない。',
    'Ready for review, tested on 3 phones.' => 'レビューの準備完了。スマホ 3 台でテスト済み。',
    'New button style applied on the invoice list and invoice page.' => '請求書一覧と請求書ページに新しいボタンのスタイルを適用した。',
    'Please check the spacing around the button and use the same size as the buttons on the order history page.' => 'ボタンの周りの余白を確認し、注文履歴ページのボタンと同じサイズにしてください。',

    // Decisions: titles
    'Use PHP and MySQL for the backend' => 'バックエンドには PHP と MySQL を使う',
    'Invoices are generated as PDF only' => '請求書は PDF のみで作成する',
    'Mobile-first layout for every screen' => 'すべての画面をモバイルファーストで作る',
    'Login with email and password, no social login' => 'メールとパスワードでログイン（SNS ログインなし）',

    // Decisions: why and what changes
    'The whole team already knows PHP and MySQL, and the school server supports them.' => 'チーム全員がすでに PHP と MySQL を知っていて、学校のサーバーでも使える。',
    'No new framework to learn. We use Composer for the few libraries we need.' => '新しいフレームワークを覚える必要がない。必要な少数のライブラリは Composer で入れる。',
    'Building both HTML and PDF invoices would take two extra weeks.' => 'HTML と PDF の両方の請求書を作ると、さらに 2 週間かかる。',
    'Customers download a PDF. The design team must create one PDF template.' => 'お客様は PDF をダウンロードする。デザインチームは PDF テンプレートを 1 つ作る必要がある。',
    'Most customers open the portal on their phone.' => 'ほとんどのお客様はスマホでポータルを開く。',
    'All designs start at 360px width. Desktop is an enlarged version.' => 'すべてのデザインは幅 360px から始める。パソコン版はそれを広げたもの。',
    'Social login needs extra privacy checks the client does not want yet.' => 'SNS ログインには追加のプライバシー確認が必要で、クライアントはまだ望んでいない。',
    'Only email and password. Passwords are stored as hashes.' => 'メールとパスワードのみ。パスワードはハッシュとして保存する。',

    // Decisions: options that were compared
    'Everyone knows it, runs on the school server.' => '全員が知っていて、学校のサーバーで動く。',
    'Modern, but only one person knows it.' => '新しいが、知っているのは 1 人だけ。',
    'HTML and PDF' => 'HTML と PDF',
    'PDF only' => 'PDF のみ',
    'One template, faster to build.' => 'テンプレートが 1 つで、早く作れる。',
    'Two templates, two extra weeks.' => 'テンプレートが 2 つで、2 週間多くかかる。',

    // Glossary
    'Handoff' => '引き継ぎ',
    'Passing finished design work to developers with all details.' => '完成したデザインを、すべての詳細と一緒に開発者へ渡すこと。',
    'Figma, task board' => 'Figma、タスクボード',
    'Invoice' => '請求書',
    'The official PDF bill for one order.' => '1 件の注文に対する正式な PDF の請求書。',
    'Invoice page' => '請求書ページ',
    'Order' => '注文',
    'Everything a customer bought in one checkout.' => 'お客様が 1 回の会計で買ったものすべて。',
    'Order history page' => '注文履歴ページ',
    'Sprint' => 'スプリント',
    'A two-to-three-week block of work with a fixed goal.' => '決まった目標を持つ 2〜3 週間の作業期間。',
    'Planning, Where are we now' => '計画、現在の状況',
    'Ticket' => 'チケット',
    'A support request a customer sends to the support team.' => 'お客様がサポートチームに送る問い合わせ。',
    'Support chat' => 'サポートチャット',

    // Questions and answers
    'Do customers need to download old invoices from before the portal existed?' => 'ポータルができる前の古い請求書も、お客様がダウンロードできる必要がありますか？',
    'Only invoices from the last 12 months. Older ones stay in the old system.' => '過去 12 か月分の請求書だけです。それより古いものは古いシステムに残ります。',
    'Which PDF template should the invoice design follow?' => '請求書のデザインはどの PDF テンプレートに合わせればいいですか？',
    'Use the template in the Figma file, page "Invoice PDF".' => 'Figma ファイルのページ「Invoice PDF」にあるテンプレートを使ってください。',
    'Where can I find the latest logo files?' => '最新のロゴファイルはどこにありますか？',

    // Say hello wall
    'Welcome to the team, Alex! Leo will be your buddy for the first weeks.' => 'チームへようこそ、Alex！最初の数週間は Leo があなたのバディです。',
    'Hi everyone, I am Sam. I build the screens customers see, so ping me about anything visual!' => 'みなさん、こんにちは。Sam です。お客様が見る画面を作っているので、見た目のことは何でも聞いてください！',

    // Job titles
    'Project Lead' => 'プロジェクトリーダー',
    'Senior Developer' => 'シニア開発者',
    'Frontend Developer' => 'フロントエンド開発者',
    'Backend Developer' => 'バックエンド開発者',
    'Junior Developer' => 'ジュニア開発者',
    'UX Designer' => 'UX デザイナー',

    // Skills
    'Accessibility' => 'アクセシビリティ',
    'Code Review' => 'コードレビュー',
    'UX Design' => 'UX デザイン',
    'Visual Design' => 'ビジュアルデザイン',

    // Project 1: Atlas
    'Invoices' => '請求書',
    'Customers can download every invoice as a PDF.' => 'お客様がすべての請求書を PDF でダウンロードできる。',
    'A web portal where customers can see their orders, download invoices and contact support in one place.' => 'お客様が注文の確認、請求書のダウンロード、サポートへの問い合わせを一か所でできるウェブポータル。',
    'Existing customers of a mid-sized online shop and the support team that answers their questions.' => '中規模オンラインショップの既存のお客様と、その質問に答えるサポートチーム。',
    'Login and order history are finished. The invoice page is in progress and support chat starts next sprint.' => 'ログインと注文履歴は完成。請求書ページは作業中で、サポートチャットは次のスプリントから始まる。',

    // Project 2: Library Booking System
    'Discovery' => '調査',
    'Agree on the booking flow with the library staff.' => '図書館のスタッフと予約の流れを決める。',
    'Online system for booking study rooms at the university library.' => '大学図書館の自習室を予約するオンラインシステム。',
    'Students who need a quiet room and the library staff who manage the rooms.' => '静かな部屋が必要な学生と、部屋を管理する図書館のスタッフ。',
    'Planning phase: wireframes are being reviewed.' => '計画段階：ワイヤーフレームをレビュー中。',

    // Milestones
    'Login and accounts' => 'ログインとアカウント',
    'Order history' => '注文履歴',
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
