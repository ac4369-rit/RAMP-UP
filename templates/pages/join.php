<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($invite !== null): ?>
  <section class="card" aria-labelledby="invite-title">
    <p class="meta"><?= e(t('join.invitedTo')) ?></p>
    <h1 id="invite-title"><?= e($invite['project_name']) ?></h1>
    <p><?= e(t('join.asRole', ['role' => t('role.' . $invite['role'])])) ?></p>

    <?php if ($inviteState === 'expired'): ?>
      <div class="notice" role="alert"><?= e(t('join.error.expired')) ?></div>
    <?php elseif ($inviteState === 'accepted'): ?>
      <div class="notice" role="status"><?= e(t('join.error.accepted')) ?></div>
      <p><a class="button primary" href="/home"><?= e(t('join.goHome')) ?></a></p>
    <?php endif; ?>
  </section>
<?php else: ?>
  <h1><?= e(t('join.loginTitle')) ?></h1>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="notice" role="alert" id="form-errors">
    <ul>
      <?php foreach ($errors as $error): ?><li><?= e(t($error)) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($invite === null || $inviteState === 'open'): ?>
  <form method="post" action="<?= $invite !== null ? '/join/' . e(rawurlencode($invite['invite_code'])) : '/login' ?>">
    <?= csrf_field() ?>

    <?php if ($signedIn !== null && $invite !== null): ?>
      <p><?= e(t('join.signedInAs', ['email' => $signedIn['email']])) ?></p>
    <?php else: ?>
      <?php if ($invite !== null): ?><h2><?= e(t('join.signIn')) ?></h2><?php endif; ?>
      <label for="email"><?= e(t('join.email')) ?></label>
      <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required
             <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>>

      <label for="password"><?= e(t('join.password')) ?></label>
      <input id="password" name="password" type="password" autocomplete="current-password" required
             <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>>
    <?php endif; ?>

    <div class="actions">
      <button type="submit" class="primary"><?= e(t($invite !== null ? 'join.accept' : 'join.confirm')) ?></button>
    </div>
  </form>
<?php endif; ?>

<?php if ($signedIn !== null): ?>
  <form method="post" action="/logout">
    <?= csrf_field() ?>
    <button type="submit"><?= e(t('join.notYou')) ?></button>
  </form>
<?php endif; ?>
