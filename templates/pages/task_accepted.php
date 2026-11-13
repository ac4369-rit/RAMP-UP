<h1><?= e(t($isFirst ? 'tasks.accepted.firstTitle' : 'tasks.accepted.title')) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>
<p><?= e(t('tasks.accepted.body', ['title' => $task['title']])) ?></p>
<?php if ($isFirst): ?><p><?= e(t('tasks.accepted.firstBody')) ?></p><?php endif; ?>

<?php if ($comment !== null && $comment['body'] !== ''): ?>
  <blockquote>
    <?= nl2br(e($comment['body'])) ?>
    <br><small class="meta"><?= e(t('tasks.feedback.from', ['name' => $comment['name'], 'date' => format_date($comment['created_at'])])) ?></small>
  </blockquote>
<?php endif; ?>

<section class="task-card">
  <h2><?= e(t('tasks.accepted.whatsNext')) ?></h2>
  <p><?= e(t('tasks.accepted.whatsNextBody')) ?></p>
</section>
<div class="actions">
  <a class="button primary" href="/tasks/starter"><?= e(t('tasks.accepted.another')) ?></a>
  <a class="button" href="/tasks"><?= e(t('tasks.back')) ?></a>
</div>

