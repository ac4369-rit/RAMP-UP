<p><a href="/tasks">← <?= e(t('tasks.back')) ?></a></p>
<h1><?= e(tc($task['title'])) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<p><?php $t = $task; require __DIR__ . '/../partials/task_meta.php'; ?></p>
<p class="meta"><?= e($task['assignee'] ? t('tasks.assignedTo', ['name' => $task['assignee']]) : t('tasks.unassigned')) ?></p>

<?php if ($isMine && $task['at_risk']): ?>
  <div class="notice" role="alert">
    <?= e(t('tasks.detail.riskNotice')) ?>
    <a href="/tasks/<?= (int) $task['id'] ?>/deadline"><?= e(t('tasks.detail.whatToDo')) ?></a>
  </div>
<?php endif; ?>

<dl>
  <dt><?= e(t('tasks.detail.description')) ?></dt>
  <dd><?= nl2br(e($task['description'] ? tc($task['description']) : t('tasks.detail.noDescription'))) ?></dd>
  <?php if ($isMine): ?>
    <dt><?= e(t('tasks.detail.nextStep')) ?></dt>
    <dd><?= e(t('tasks.next.' . $task['status'])) ?></dd>
  <?php endif; ?>
</dl>

<?php if ($isMine): ?>
  <div class="actions">
    <?php if ($task['status'] === 'done'): ?>
      <a class="button primary" href="/tasks/<?= (int) $task['id'] ?>/accepted"><?= e(t('tasks.detail.seeResult')) ?></a>
    <?php else: ?>
      <?php if ($hasHandoff): ?><a class="button" href="/tasks/<?= (int) $task['id'] ?>/handoff"><?= e(t('tasks.detail.handoff')) ?></a><?php endif; ?>
      <?php if ($hasFeedback): ?><a class="button" href="/tasks/<?= (int) $task['id'] ?>/feedback"><?= e(t('tasks.detail.feedback')) ?></a><?php endif; ?>
      <?php if ($task['status'] !== 'review'): ?>
        <a class="button primary" href="/tasks/<?= (int) $task['id'] ?>/progress"><?= e(t('tasks.detail.progress')) ?></a>
        <a class="button" href="/tasks/<?= (int) $task['id'] ?>/review"><?= e(t('tasks.detail.review')) ?></a>
        <?php if ($task['status'] !== 'blocked'): ?>
          <a class="button" href="/tasks/<?= (int) $task['id'] ?>/blocked"><?= e(t('tasks.detail.blocked')) ?></a>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php elseif ($task['assigned_to'] === null && $task['is_starter']): ?>
  <form method="post" action="/tasks/<?= (int) $task['id'] ?>/take">
    <?= csrf_field() ?>
    <button type="submit" class="primary"><?= e(t('tasks.starter.take', ['title' => tc($task['title'])])) ?></button>
  </form>
<?php endif; ?>

<?php if ($canReview): ?>
  <section aria-labelledby="decide" class="card">
    <h2 id="decide"><?= e(t('tasks.decide.title')) ?></h2>
    <?php if ($errors): ?>
      <div class="notice" role="alert" id="form-errors">
        <ul><?php foreach ($errors as $error): ?><li><?= e(t($error, ['max' => 1000])) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>
    <form method="post" action="/tasks/<?= (int) $task['id'] ?>/decide">
      <?= csrf_field() ?>
      <label for="comment"><?= e(t('tasks.decide.comment')) ?></label>
      <p class="meta" id="comment-hint"><?= e(t('tasks.decide.commentHint')) ?></p>
      <textarea id="comment" name="comment" rows="4" maxlength="1000"
                aria-describedby="comment-hint<?= $errors ? ' form-errors' : '' ?>"><?= e($comment) ?></textarea>
      <div class="actions">
        <button type="submit" name="action" value="accept" class="primary"><?= e(t('tasks.decide.accept')) ?></button>
        <button type="submit" name="action" value="changes"><?= e(t('tasks.decide.changes')) ?></button>
      </div>
    </form>
  </section>
<?php endif; ?>

<section aria-labelledby="history">
  <h2 id="history"><?= e(t('tasks.detail.history')) ?></h2>
  <?php if (!$history): ?>
    <?php $state = 'empty'; $messageKey = 'tasks.detail.noHistory'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <ol class="list">
      <?php foreach ($history as $h): ?>
        <li>
          <strong class="tag"><?= e(t('tasks.kind.' . $h['kind'])) ?></strong>
          <small class="meta"><?= e($h['name']) ?> · <?= e(format_datetime($h['created_at'])) ?></small>
          <?php if ($h['body'] !== ''): ?><p><?= nl2br(e(tc($h['body']))) ?></p><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
</section>
