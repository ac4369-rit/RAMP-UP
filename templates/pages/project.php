<h1><?= e(t('project.title')) ?></h1>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'home.noProject'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <p class="meta"><?= e($project['name']) ?></p>
  <dl>
    <dt><?= e(t('project.what')) ?></dt>
    <dd><?= nl2br(e(tc($project['description'] ?? t('project.unknown')))) ?></dd>

    <dt><?= e(t('project.forWho')) ?></dt>
    <dd><?= nl2br(e(tc($project['audience'] ?? t('project.unknown')))) ?></dd>

    <dt><?= e(t('project.whereWeStand')) ?></dt>
    <dd>
      <?= nl2br(e(tc($project['current_status'] ?? t('project.unknown')))) ?>
      <br><small class="meta">
        <?= e(format_date($project['sprint_starts_on'])) ?> – <?= e(format_date($project['sprint_ends_on'])) ?>
        · <?= e(t('project.daysLeft', ['count' => $daysLeft])) ?>
      </small>
    </dd>
  </dl>
  <div class="actions"><a class="button" href="/status"><?= e(t('project.timeline')) ?></a></div>
<?php endif; ?>
