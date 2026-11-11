<?php /** @var array $groups @var string $state @var string $syncedAt @var string $role */ ?>
<h1><?= e(t('resources.title')) ?></h1>
<p class="meta"><?= e(t('resources.intro', ['role' => t('role.' . $role)])) ?></p>
<p class="meta"><?= e(t('resources.lastSynced', ['when' => format_datetime($syncedAt)])) ?></p>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'resources.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <?php foreach ($groups as $kind => $items): ?>
    <h2><?= e(t('resources.kind.' . $kind)) ?></h2>
    <ul class="list">
      <?php foreach ($items as $r): ?>
        <li>
          <a href="<?= e($r['url']) ?>" rel="noopener noreferrer"><?= e($r['title']) ?></a><br>
          <?php if ($r['trust'] === 'verified'): ?>
            <span class="status--verified">✓ <?= e(t('resources.verified')) ?></span>
            <small class="meta"><?= e(t('resources.verifiedBy', ['name' => (string) $r['verified_by'], 'when' => format_date($r['verified_at'])])) ?></small>
          <?php elseif ($r['trust'] === 'outdated'): ?>
            <span class="status--outdated">✗ <?= e(t('resources.outdated')) ?></span>
            <small class="meta"><?= e(t('resources.lastVerified', ['when' => format_date($r['verified_at'])])) ?></small>
            <br><small><a href="/ask?q=<?= e(rawurlencode($r['title'])) ?>"><?= e(t('resources.checkFirst')) ?></a></small>
          <?php else: ?>
            <span class="status--unverified">⚠ <?= e(t('resources.unverified')) ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>
<?php endif; ?>
