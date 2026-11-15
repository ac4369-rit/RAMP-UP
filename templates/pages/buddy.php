<h1><?= e(t('buddy.title')) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php elseif ($state === 'empty'): ?>
  <?php $messageKey = 'buddy.empty'; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <section class="card" aria-labelledby="buddy-name">
    <p class="meta"><?= e(t('buddy.yourBuddy')) ?></p>
    <h2 id="buddy-name"><?= e($buddy['name']) ?></h2>
    <?php if ($buddy['job_title']): ?><p class="meta"><?= e(tc($buddy['job_title'])) ?></p><?php endif; ?>
  </section>

  <h2><?= e(t('buddy.book', ['minutes' => 15])) ?></h2>

  <?php if ($call !== null): ?>
    <p role="status"><?= e(t('buddy.booked', ['day' => weekday_name((int) $call['weekday']), 'minutes' => (int) $call['minutes']])) ?>
      · <?= e(t('buddy.status.' . $call['status'])) ?></p>
    <p><a class="button" href="/buddy/<?= (int) $call['id'] ?>/cancel"><?= e(t('buddy.cancel')) ?></a></p>
  <?php else: ?>
    <form method="post" action="/buddy">
      <?= csrf_field() ?>
      <fieldset>
        <legend><?= e(t('buddy.pickDay')) ?></legend>
        <ul class="days plain">
          <?php foreach ($days as $d): ?>
            <?php $free = in_array($d, $buddyDays, true); ?>
            <li>
              <input id="call-day-<?= $d ?>" type="radio" name="weekday" value="<?= $d ?>" required <?= $free ? '' : 'disabled' ?>>
              <label for="call-day-<?= $d ?>">
                <?= e(weekday_name($d)) ?>
                <?php if (!$free): ?><small class="meta">(<?= e(t('buddy.busy')) ?>)</small><?php endif; ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </fieldset>
      <div class="actions"><button type="submit" class="primary"><?= e(t('buddy.confirm')) ?></button></div>
    </form>
  <?php endif; ?>
<?php endif; ?>
