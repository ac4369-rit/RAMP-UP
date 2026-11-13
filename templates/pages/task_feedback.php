<p><a href="/tasks/<?= (int) $task['id'] ?>">← <?= e(t('tasks.backToTask')) ?></a></p>
<h1><?= e(t('tasks.feedback.title')) ?></h1>
<p class="meta"><?= e($task['title']) ?></p>

<?php if (!$feedback): ?>
  <?php $state = 'empty'; $messageKey = 'tasks.feedback.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <?php if ($task['status'] === 'in_progress'): ?><p><?= e(t('tasks.feedback.intro')) ?></p><?php endif; ?>
  <section class="task-card">
    <h2><?= e(t('tasks.feedback.changes')) ?></h2>
    <?php foreach ($feedback as $i => $f): ?>
      <p<?= $i > 0 ? ' class="meta"' : '' ?>><?= nl2br(e($f['body'])) ?>
        <br><small class="meta"><?= e(t('tasks.feedback.from', ['name' => $f['name'], 'date' => format_date($f['created_at'])])) ?></small></p>
    <?php endforeach; ?>

    <h2><?= e(t('tasks.feedback.nextStep')) ?></h2>
    <p><?= e(t('tasks.feedback.nextStepBody')) ?></p>
  </section>
  <?php if ($task['status'] !== 'done'): ?>
    <div class="actions"><a class="button primary" href="/tasks/<?= (int) $task['id'] ?>/progress"><?= e(t('tasks.feedback.update')) ?></a></div>
  <?php endif; ?>
<?php endif; ?>
