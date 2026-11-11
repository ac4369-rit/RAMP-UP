<?php /** @var array $decisions @var list<string> $areas @var string $q @var string $area @var string $state */ ?>
<h1><?= e(t('decisions.title')) ?></h1>

<form method="get" action="/decisions" data-async class="filters" role="search">
  <label for="q"><?= e(t('decisions.search')) ?></label>
  <input id="q" type="search" name="q" value="<?= e($q) ?>">

  <label for="area"><?= e(t('decisions.area')) ?></label>
  <select id="area" name="area">
    <option value=""><?= e(t('decisions.allAreas')) ?></option>
    <?php foreach ($areas as $a): ?>
      <option value="<?= e($a) ?>" <?= $a === $area ? 'selected' : '' ?>><?= e(t('role.' . $a)) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit"><?= e(t('decisions.apply')) ?></button>
</form>

<div <?= async_attrs() ?>>
  <?php if ($state === 'error'): ?>
    <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
  <?php elseif ($state === 'empty'): ?>
    <?php $messageKey = 'decisions.empty'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <p aria-live="polite"><?= e(t('decisions.count', ['count' => count($decisions)])) ?></p>
    <table>
      <caption class="visually-hidden"><?= e(t('decisions.title')) ?></caption>
      <thead>
        <tr>
          <th scope="col"><?= e(t('decisions.col.decision')) ?></th>
          <th scope="col"><?= e(t('decisions.col.who')) ?></th>
          <th scope="col"><?= e(t('decisions.col.status')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($decisions as $d): ?>
          <tr>
            <td>
              <a href="/decisions/<?= (int) $d['id'] ?>"><?= e($d['title']) ?></a>
              <br><small><?= e(format_date($d['decided_at'])) ?></small>
              <?php if ((int) $d['affects_me']): ?><br><strong class="badge badge--role"><?= e(t('decisions.affectsMe')) ?></strong><?php endif; ?>
              <?php if (!(int) $d['is_read']): ?><strong class="badge"><?= e(t('decisions.unread')) ?></strong><?php endif; ?>
            </td>
            <td><?= e($d['decided_by']) ?></td>
            <td><?= e(t('decisions.status.' . $d['status'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
