<p><a href="/tasks/<?= (int) $task['id'] ?>">← <?= e(t('tasks.backToTask')) ?></a></p>
<h1><?= e(t('tasks.handoff.title')) ?></h1>
<p class="meta"><?= e(tc($task['title'])) ?></p>
<p><?= e(t('tasks.handoff.intro')) ?></p>

<?php if (!$notes): ?>
  <?php $state = 'empty'; $messageKey = 'tasks.handoff.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <?php foreach ($notes as $n): ?>
    <section class="task-card">
      <h2><?= e(t('tasks.handoff.from', ['name' => $n['name'], 'date' => format_date($n['created_at'])])) ?></h2>
      <p><?= nl2br(e($n['body'])) ?></p>
    </section>
  <?php endforeach; ?>
<?php endif; ?>

<div class="actions"><a class="button" href="/tasks/<?= (int) $task['id'] ?>"><?= e(t('tasks.backToTask')) ?></a></div>
