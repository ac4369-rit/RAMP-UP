<?php /** @var array $form @var list<string> $errors @var ?array $match @var list<string> $roles @var array $team @var array $mine @var ?string $decisionTitle */ ?>
<h1><?= e(t('ask.title')) ?></h1>
<p class="meta"><?= e(t('ask.intro')) ?></p>

<?php if ($decisionTitle !== null): ?>
  <p class="notice"><?= e(t('ask.related', ['title' => $decisionTitle])) ?></p>
<?php endif; ?>

<?php if ($match): ?>
  <div class="notice" role="status">
    <strong><?= e(t('ask.existing.title')) ?></strong>
    <p><?= e($match['title']) ?></p>
    <a class="button" href="<?= $match['type'] === 'decision' ? '/decisions/' : '/questions/' ?><?= (int) $match['id'] ?>">
      <?= e(t($match['type'] === 'decision' ? 'ask.existing.viewDecision' : 'ask.existing.viewThread')) ?>
    </a>
    <p class="meta"><?= e(t('ask.existing.body')) ?></p>
  </div>
<?php endif; ?>

<?php
$action = '/ask'; $submitKey = 'ask.submit';
require __DIR__ . '/../partials/question_form.php';
?>

<section aria-labelledby="mine">
  <h2 id="mine"><?= e(t('ask.yourQuestions')) ?></h2>
  <?php if (!$mine): ?>
    <?php $state = 'empty'; $messageKey = 'ask.none'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <ul class="list">
      <?php foreach ($mine as $m): ?>
        <li>
          <a href="/questions/<?= (int) $m['id'] ?>"><?= e(mb_strimwidth($m['body'], 0, 80, '…')) ?></a><br>
          <small class="meta"><?= e(t('ask.replies', ['count' => (int) $m['replies']])) ?> · <?= e(format_date($m['created_at'])) ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
