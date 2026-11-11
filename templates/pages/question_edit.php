<?php /** @var array $question @var array $form @var list<string> $errors @var list<string> $roles @var array $team */ ?>
<p><a href="/questions/<?= (int) $question['id'] ?>">← <?= e(t('thread.back')) ?></a></p>
<h1><?= e(t('ask.edit')) ?></h1>
<?php
$action = '/questions/' . (int) $question['id'] . '/edit'; $submitKey = 'ask.save'; $match = null;
require __DIR__ . '/../partials/question_form.php';
?>
