<?php /** @var string $state; si ready: $total $read $gaps $readingPct $quiz $unanswered $quizUrl $readiness $level */ ?>
<h1><?= e(t('check.title')) ?></h1>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <p>
    <?php if ($quizUrl): ?>
      <a class="button primary" href="<?= e($quizUrl) ?>" target="_blank" rel="noopener noreferrer"><?= e(t('check.quizLink')) ?></a>
    <?php else: ?>
      <span class="meta"><?= e(t('check.noQuiz')) ?></span>
    <?php endif; ?>
  </p>

  <section aria-labelledby="ready">
    <h2 id="ready"><?= e(t('check.readiness')) ?></h2>
    <p class="score"><?= e(format_number($readiness)) ?>%</p>
    <progress max="100" value="<?= (int) $readiness ?>" aria-label="<?= e(t('check.readiness')) ?>"><?= (int) $readiness ?>%</progress>
    <p role="status"><strong><?= e(t('check.level.' . $level)) ?></strong></p>
  </section>

  <h2><?= e(t('check.breakdown')) ?></h2>
  <ul class="list">
    <li><?= e(t('check.reading')) ?>: <?= e(t('check.readCount', ['read' => $read, 'total' => $total])) ?> (<?= (int) $readingPct ?>%)</li>
    <li><?= e(t('check.quiz')) ?>:
      <?php if ($quiz): ?><?= (int) $quiz['score_percent'] ?>% — <?= e(format_date($quiz['taken_at'])) ?>
      <?php else: ?><?= e(t('check.quizNone')) ?><?php endif; ?>
    </li>
    <?php if ($unanswered > 0): ?>
      <li><?= e(t('check.unanswered', ['count' => $unanswered])) ?> — <a href="/ask"><?= e(t('check.seeQuestions')) ?></a></li>
    <?php endif; ?>
  </ul>

  <h2><?= e(t('check.gaps')) ?></h2>
  <?php if (!$gaps): ?>
    <?php $state = 'empty'; $messageKey = 'check.gapsNone'; require __DIR__ . '/../partials/states.php'; ?>
  <?php else: ?>
    <ul class="list">
      <?php foreach ($gaps as $g): ?>
        <li><a href="/decisions/<?= (int) $g['id'] ?>"><?= e($g['title']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h2><?= e(t('check.saveTitle')) ?></h2>
  <form method="post" action="/check">
    <?= csrf_field() ?>
    <label for="score"><?= e(t('check.score')) ?></label>
    <input id="score" name="score" type="number" min="0" max="100" inputmode="numeric" required>
    <div class="actions"><button type="submit"><?= e(t('check.saveScore')) ?></button></div>
  </form>
<?php endif; ?>
