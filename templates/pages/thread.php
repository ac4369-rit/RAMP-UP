<?php /** @var array $question @var array $answers @var bool $isAuthor */ ?>
<h1><?= e(t('thread.title')) ?></h1>

<blockquote>
  <?= nl2br(e(tc($question['body']))) ?>
  <br><small class="meta">
    <?= e(t('thread.askedBy', ['name' => $question['author_name']])) ?>
    · <?= e(format_datetime($question['created_at'])) ?>
    · <?= e(t('role.' . $question['area'])) ?>
    <?php if ($question['to_name']): ?> · <?= e(t('thread.to', ['name' => $question['to_name']])) ?><?php endif; ?>
  </small>
</blockquote>

<?php if ($question['related_decision_id']): ?>
  <p><a href="/decisions/<?= (int) $question['related_decision_id'] ?>"><?= e(t('thread.relatedDecision', ['title' => tc($question['decision_title'])])) ?></a></p>
<?php endif; ?>

<?php if ($isAuthor): ?>
  <div class="actions">
    <a class="button" href="/questions/<?= (int) $question['id'] ?>/edit"><?= e(t('thread.edit')) ?></a>
    <a class="button" href="/questions/<?= (int) $question['id'] ?>/delete"><?= e(t('thread.delete')) ?></a>
  </div>
<?php endif; ?>

<h2><?= e(t('ask.replies', ['count' => count($answers)])) ?></h2>
<?php if (!$answers): ?>
  <?php $state = 'empty'; $messageKey = 'thread.noReplies'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <ol class="list">
    <?php foreach ($answers as $a): ?>
      <li id="answer-<?= (int) $a['id'] ?>">
        <strong><?= e($a['name']) ?></strong>
        <small class="meta"><?= e(format_datetime($a['created_at'])) ?></small>
        <p><?= nl2br(e(tc($a['body']))) ?></p>
      </li>
    <?php endforeach; ?>
  </ol>
<?php endif; ?>

<form method="post" action="/questions/<?= (int) $question['id'] ?>/answers">
  <?= csrf_field() ?>
  <label for="answer"><?= e(t('thread.answerLabel')) ?></label>
  <textarea id="answer" name="body" rows="3" maxlength="2000" required></textarea>
  <div class="actions"><button type="submit" class="primary"><?= e(t('thread.send')) ?></button></div>
</form>
