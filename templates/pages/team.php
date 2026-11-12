<h1><?= e(t('team.title')) ?></h1>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'team.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <p class="meta"><?= e(t('team.count', ['count' => count($members)])) ?></p>
  <ul class="list">
    <?php foreach ($members as $m): ?>
      <li>
        <strong><?= e($m['name']) ?></strong>
        <?php if ((int) $m['id'] === $me): ?><span class="badge"><?= e(t('team.you')) ?></span><?php endif; ?>
        <?php if ((int) $m['is_buddy']): ?><span class="badge badge--role"><?= e(t('team.buddy')) ?></span><?php endif; ?>
        <br><small class="meta"><?= e(t('role.' . $m['role'])) ?><?php if ($m['job_title']): ?> · <?= e($m['job_title']) ?><?php endif; ?></small>
        <?php if ((int) $m['id'] === $me && $m['replaces_name']): ?>
          <p><?= e(t('team.replacing', ['name' => $m['replaces_name']])) ?></p>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
