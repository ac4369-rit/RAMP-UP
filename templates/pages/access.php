<h1><?= e(t('access.title')) ?></h1>
<p class="meta"><?= e(t('access.intro')) ?></p>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'access.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <p aria-live="polite"><?= e(t('access.summary', ['count' => $granted, 'total' => count($tools)])) ?></p>
  <ul class="list">
    <?php foreach ($tools as $tool): ?>
      <li>
        <strong><?= e($tool['name']) ?></strong><br>
        <?php if ($tool['status'] === 'granted'): ?>
          <span class="status--verified">✓ <?= e(t('access.granted')) ?></span>
          <?php if ($tool['url']): ?> · <a href="<?= e($tool['url']) ?>" rel="noopener noreferrer"><?= e(t('access.open', ['tool' => $tool['name']])) ?></a><?php endif; ?>
        <?php else: ?>
          <?php if ($tool['status'] === 'pending'): ?>
            <span class="status--unverified">⧗ <?= e(t('access.pending')) ?></span>
          <?php else: ?>
            <span class="status--outdated">✗ <?= e(t('access.none')) ?></span>
          <?php endif; ?>
          <form method="post" action="/access/<?= (int) $tool['id'] ?>" class="inline">
            <?= csrf_field() ?>
            <?php if ($tool['status'] === null): ?>
              <button type="submit" name="action" value="request"><?= e(t('access.request', ['tool' => $tool['name']])) ?></button>
            <?php endif; ?>
            <button type="submit" name="action" value="works"><?= e(t('access.works', ['tool' => $tool['name']])) ?></button>
          </form>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
