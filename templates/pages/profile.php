<h1><?= e(t('profile.title')) ?></h1>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($state === 'error'): ?>
  <?php $retryHref = $_SERVER['REQUEST_URI']; require __DIR__ . '/../partials/states.php'; ?>
<?php else: ?>
  <div class="profile-head">
    <span class="avatar" aria-hidden="true"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
    <p class="name"><?= e($user['name']) ?></p>
  </div>

  <?php if ($errors): ?>
    <div class="notice" role="alert" id="form-errors">
      <ul><?php foreach ($errors as $error): ?><li><?= e(t($error)) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/profile">
    <?= csrf_field() ?>

    <fieldset>
      <legend><?= e(t('profile.skills')) ?></legend>
      <?php if (!$skills): ?>
        <p class="meta"><?= e(t('profile.noSkills')) ?></p>
      <?php else: ?>
        <p class="meta" id="skills-hint"><?= e(t('profile.skillsHint')) ?></p>
        <ul class="chips plain">
          <?php foreach ($skills as $i => $skill): ?>
            <li>
              <input id="skill-<?= $i ?>" type="checkbox" name="keep[]" value="<?= e($skill) ?>" checked aria-describedby="skills-hint">
              <label for="skill-<?= $i ?>"><?= e($skill) ?></label>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <label for="new_skill"><?= e(t('profile.addSkill')) ?></label>
      <input id="new_skill" name="new_skill" type="text" maxlength="60">
    </fieldset>

    <fieldset>
      <legend><?= e(t('profile.availability')) ?></legend>
      <ul class="days plain">
        <?php for ($d = 1; $d <= 7; $d++): ?>
          <li>
            <input id="day-<?= $d ?>" type="checkbox" name="days[]" value="<?= $d ?>" <?= in_array($d, $days, true) ? 'checked' : '' ?>>
            <label for="day-<?= $d ?>"><?= e(weekday_name($d)) ?></label>
          </li>
        <?php endfor; ?>
      </ul>
    </fieldset>

    <div class="actions"><button type="submit" class="primary"><?= e(t('profile.save')) ?></button></div>
  </form>
<?php endif; ?>
