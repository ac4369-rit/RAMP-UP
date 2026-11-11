<?php /** @var array $question */ ?>
<h1><?= e(t('delete.title')) ?></h1>
<p><?= e(t('delete.body')) ?></p>
<blockquote><?= nl2br(e($question['body'])) ?></blockquote>
<form method="post" action="/questions/<?= (int) $question['id'] ?>/delete">
  <?= csrf_field() ?>
  <div class="actions">
    <button type="submit" class="danger"><?= e(t('delete.confirm')) ?></button>
    <a class="button" href="/questions/<?= (int) $question['id'] ?>"><?= e(t('delete.cancel')) ?></a>
  </div>
</form>
