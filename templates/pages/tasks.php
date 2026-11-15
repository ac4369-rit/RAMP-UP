<h1><?= e(t('tasks.title')) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'tasks.empty'; require __DIR__ . '/../partials/states.php'; ?>
  <p><?= e(t('tasks.emptyHint')) ?></p>
  <div class="actions"><a class="button primary" href="/tasks/starter"><?= e(t('tasks.findStarter')) ?></a></div>
<?php else: ?>
  <ul class="list">
    <?php foreach ($tasks as $t): ?>
      <li class="task-card">
        <h2><a href="/tasks/<?= (int) $t['id'] ?>"><?= e(tc($t['title'])) ?></a></h2>
        <?php require __DIR__ . '/../partials/task_meta.php'; ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="actions"><a class="button" href="/tasks/starter"><?= e(t('tasks.findStarter')) ?></a></div>
<?php endif; ?>

<?php if ($toReview): ?>
  <section aria-labelledby="to-review">
    <h2 id="to-review"><?= e(t('tasks.reviewQueue')) ?></h2>
    <ul class="list">
      <?php foreach ($toReview as $t): ?>
        <li>
          <a href="/tasks/<?= (int) $t['id'] ?>"><?= e(tc($t['title'])) ?></a>
          <br><small class="meta"><?= e(t('tasks.assignedTo', ['name' => $t['assignee']])) ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>
