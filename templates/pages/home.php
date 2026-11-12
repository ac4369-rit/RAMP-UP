<h1><?= e(t('home.hi', ['name' => $user['name']])) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($project === null): ?>
  <?php $state = 'empty'; $messageKey = 'home.noProject'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <p class="meta"><?= e(t('home.project', ['project' => $project['name']])) ?></p>

  <ul class="cards plain">
    <li><a class="card-link" href="/project"><?= e(t('project.title')) ?></a></li>
    <li><a class="card-link" href="/team"><?= e(t('team.title')) ?></a></li>
    <li><a class="card-link" href="/missed"><?= e(t('home.missed')) ?></a></li>
    <li>
      <h2><?= e(t('home.firstTask')) ?></h2>
      <?php if ($task !== null): ?>
        <p><?= e($task['title']) ?></p>
        <?php if ($task['due_date']): ?><small class="meta"><?= e(t('home.due', ['date' => format_date($task['due_date'])])) ?></small><?php endif; ?>
      <?php else: ?>
        <p class="meta"><?= e(t('home.noTask')) ?></p>
      <?php endif; ?>
    </li>
  </ul>

  <h2><?= e(t('home.steps')) ?></h2>
  <ol class="list">
    <li><a href="/profile"><?= e(t('profile.title')) ?></a></li>
    <li><a href="/access"><?= e(t('access.title')) ?></a></li>
    <li><a href="/buddy"><?= e(t('buddy.title')) ?></a></li>
    <li><a href="/hello"><?= e(t('hello.title')) ?></a></li>
    <li><a href="/rules"><?= e(t('rules.title')) ?></a></li>
    <li><a href="/notifications"><?= e(t('notify.title')) ?></a></li>
  </ol>
<?php endif; ?>
