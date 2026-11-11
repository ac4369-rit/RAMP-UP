<?php
/** @var array $form @var list<string> $errors @var list<string> $roles @var array $team
 *  @var string $action @var string $submitKey @var ?array $match */
$match = $match ?? null;
?>
<form method="post" action="<?= e($action) ?>" novalidate>
  <?= csrf_field() ?>

  <?php if ($errors): ?>
    <div class="notice notice--error" role="alert">
      <ul><?php foreach ($errors as $k): ?><li><?= e(t($k)) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <label for="body"><?= e(t('ask.body')) ?></label>
  <textarea id="body" name="body" rows="4" maxlength="1000" required><?= e($form['body']) ?></textarea>

  <label for="area"><?= e(t('ask.area')) ?></label>
  <select id="area" name="area">
    <?php foreach ($roles as $r): ?>
      <option value="<?= e($r) ?>" <?= $r === $form['area'] ? 'selected' : '' ?>><?= e(t('role.' . $r)) ?></option>
    <?php endforeach; ?>
  </select>

  <label for="to"><?= e(t('ask.to')) ?></label>
  <select id="to" name="to">
    <option value=""><?= e(t('ask.anyone')) ?></option>
    <?php foreach ($team as $m): ?>
      <option value="<?= (int) $m['id'] ?>" <?= (string) $m['id'] === $form['to'] ? 'selected' : '' ?>>
        <?= e($m['name']) ?> (<?= e(t('role.' . $m['role'])) ?>)
      </option>
    <?php endforeach; ?>
  </select>

  <input type="hidden" name="related" value="<?= e($form['related']) ?>">
  <?php if ($match): ?><input type="hidden" name="force" value="1"><?php endif; ?>

  <div class="actions">
    <button type="submit" class="primary"><?= e(t($match ? 'ask.askAnyway' : $submitKey)) ?></button>
  </div>
</form>
