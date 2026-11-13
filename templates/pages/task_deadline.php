<p><a href="/tasks/<?= (int) $task['id'] ?>">← <?= e(t('tasks.backToTask')) ?></a></p>
<h1><?= e(t('tasks.deadline.title')) ?></h1>
<p class="meta"><?= e($task['title']) ?></p>
<p><?php $t = $task; require __DIR__ . '/../partials/task_meta.php'; ?></p>

<?php if ($task['due_date'] === null): ?>
  <?php $state = 'empty'; $messageKey = 'tasks.deadline.noDue'; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif (!$task['at_risk']): ?>
  <div class="notice" role="status"><?= e(t('tasks.deadline.onTrack')) ?></div>
<?php else: ?>
  <p><?= e(t('tasks.deadline.intro')) ?></p>
  <section class="task-card">
    <h2><?= e(t('tasks.deadline.whatToDo')) ?></h2>
    <p><?= e(t('tasks.deadline.whatToDoBody')) ?></p>
  </section>
  <div class="actions">
    <a class="button" href="/tasks/<?= (int) $task['id'] ?>/blocked"><?= e(t('tasks.deadline.report')) ?></a>
    <a class="button primary" href="/tasks/<?= (int) $task['id'] ?>/progress"><?= e(t('tasks.deadline.update')) ?></a>
  </div>
<?php endif; ?>
