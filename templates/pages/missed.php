<?php /** @var array $user @var string $role @var list<string> $roles @var array $forRole @var array $others @var string $state @var array $github */ ?>
<h1><?= e(t('missed.welcome', ['name' => $user['name']])) ?></h1>
<p class="meta"><?= e(t('missed.intro')) ?></p>

<form method="get" action="/missed" data-async class="filters">
  <label for="role"><?= e(t('missed.role')) ?></label>
  <select id="role" name="role" onchange="this.form.requestSubmit()">
    <?php foreach ($roles as $r): ?>
      <option value="<?= e($r) ?>" <?= $r === $role ? 'selected' : '' ?>><?= e(t('role.' . $r)) ?></option>
    <?php endforeach; ?>
  </select>
  <noscript><button type="submit"><?= e(t('decisions.apply')) ?></button></noscript>
</form>

<div <?= async_attrs() ?>>
  <?php if ($state === 'error'): ?>
    <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
  <?php elseif ($state === 'empty'): ?>
    <?php $messageKey = 'missed.empty'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <?php if ($github['stale'] || $github['error']): ?>
      <div class="notice" role="status"><?= e(t($github['stale'] ? 'missed.githubStale' : 'missed.githubDown')) ?></div>
    <?php endif; ?>

    <h2><?= e(t('missed.forRole', ['role' => t('role.' . $role)])) ?></h2>
    <?php if ($forRole): $items = $forRole; require __DIR__ . '/../partials/event_list.php'; ?>
    <?php else: ?><p class="meta"><?= e(t('missed.nothingForRole')) ?></p><?php endif; ?>

    <?php if ($others): ?>
      <h2><?= e(t('missed.acrossProject')) ?></h2>
      <?php $items = $others; require __DIR__ . '/../partials/event_list.php'; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<div class="actions">
  <a class="button" href="/decisions"><?= e(t('missed.readMore')) ?></a>
  <a class="button" href="/status"><?= e(t('missed.whereAreWe')) ?></a>
</div>
