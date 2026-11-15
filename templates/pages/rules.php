<h1><?= e(t('rules.title')) ?></h1>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'rules.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <h2><?= e(t('rules.heading')) ?></h2>
  <ol class="list">
    <?php foreach ($rules as $rule): ?>
      <li><?= e(tc($rule['rule_text'])) ?></li>
    <?php endforeach; ?>
  </ol>
<?php endif; ?>
