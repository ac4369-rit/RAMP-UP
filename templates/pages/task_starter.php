<h1><?= e(t('tasks.starter.title')) ?></h1>
<p class="meta"><?= e(t('tasks.starter.intro')) ?></p>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'tasks.starter.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <ul class="list plain">
    <?php foreach ($tasks as $t): ?>
      <li class="task-card">
        <h2><?= e(tc($t['title'])) ?></h2>
        <?php if ($t['description']): ?><p><?= e($t['description']) ?></p><?php endif; ?>
        <p><?php $t['status'] = 'todo'; require __DIR__ . '/../partials/task_meta.php'; ?></p>
        <form method="post" action="/tasks/<?= (int) $t['id'] ?>/take" class="inline">
          <?= csrf_field() ?>
          <button type="submit" class="primary"><?= e(t('tasks.starter.take', ['title' => tc($t['title'])])) ?></button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<p><a href="/tasks">← <?= e(t('tasks.back')) ?></a></p>
