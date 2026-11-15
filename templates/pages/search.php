<?php /** @var string $q @var string $type @var array $results @var string $state */ ?>
<h1><?= e(t('search.title')) ?></h1>

<form method="get" action="/search" data-async class="filters" role="search">
  <label for="q"><?= e(t('search.label')) ?></label>
  <input id="q" type="search" name="q" value="<?= e($q) ?>">

  <label for="type"><?= e(t('search.type')) ?></label>
  <select id="type" name="type">
    <?php foreach (['all', 'decisions', 'glossary', 'questions'] as $o): ?>
      <option value="<?= $o ?>" <?= $o === $type ? 'selected' : '' ?>><?= e(t('search.types.' . $o)) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit"><?= e(t('decisions.apply')) ?></button>
</form>

<div <?= async_attrs() ?>>
  <?php if ($state === 'idle'): ?>
    <div class="state" role="status"><p><?= e(t('search.idle')) ?></p></div>
  <?php elseif ($state === 'error'): ?>
    <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
  <?php elseif ($state === 'empty'): ?>
    <?php $messageKey = 'search.empty'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <p aria-live="polite"><?= e(t('search.count', ['count' => count($results)])) ?></p>
    <ul class="list">
      <?php foreach ($results as $r): ?>
        <li>
          <small class="tag"><?= e(t('search.types.' . $r['type'])) ?></small><br>
          <a href="<?= e($r['href']) ?>"><?= e(tc($r['title'])) ?></a>
          <?php if ($r['snippet'] !== ''): ?><br><small class="meta"><?= e($r['snippet']) ?></small><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<p class="meta"><?= e(t('search.stillNeed')) ?></p>
<p><a class="button primary" href="/ask?q=<?= e(rawurlencode($q)) ?>"><?= e(t('search.askTeam')) ?></a></p>
