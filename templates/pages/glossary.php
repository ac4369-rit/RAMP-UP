<?php /** @var array $terms @var string $q @var string $state */ ?>
<h1><?= e(t('glossary.title')) ?></h1>

<form method="get" action="/glossary" data-async class="filters" role="search">
  <label for="q"><?= e(t('glossary.search')) ?></label>
  <input id="q" type="search" name="q" value="<?= e($q) ?>">
  <button type="submit"><?= e(t('decisions.apply')) ?></button>
</form>

<div <?= async_attrs() ?>>
  <?php if ($state === 'error'): ?>
    <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
  <?php elseif ($state === 'empty'): ?>
    <?php $messageKey = 'glossary.empty'; require __DIR__ . '/../partials/states.php'; ?>
    <p><a class="button" href="/ask?q=<?= e(rawurlencode($q)) ?>"><?= e(t('glossary.askTeam')) ?></a></p>
  <?php else: ?>
    <p aria-live="polite"><?= e(t('glossary.count', ['count' => count($terms)])) ?></p>
    <table>
      <caption class="visually-hidden"><?= e(t('glossary.title')) ?></caption>
      <thead>
        <tr>
          <th scope="col"><?= e(t('glossary.term')) ?></th>
          <th scope="col"><?= e(t('glossary.definition')) ?></th>
          <th scope="col"><?= e(t('glossary.used')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($terms as $term): ?>
          <tr>
            <th scope="row"><?= e($term['term']) ?></th>
            <td><?= e($term['definition']) ?></td>
            <td><?= e($term['used_in'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
