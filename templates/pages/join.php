<?php require __DIR__ . '/../partials/flash.php'; ?>

<?php if ($invite === null): ?>
  <h1><?= e(t('join.loginTitle')) ?></h1>
<?php endif; ?>

<?php if ($invite !== null): ?>
<section class="card invite" aria-labelledby="invite-title">
  <div class="invite__head">
    <span class="invite__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
    </span>
    <div>
      <p class="meta"><?= e(t('join.invitedTo')) ?></p>
      <h1 id="invite-title"><?= e($invite['project_name']) ?></h1>
      <p><span class="badge badge--role"><?= e(t('join.asRole', ['role' => t('role.' . $invite['role'])])) ?></span></p>
    </div>
  </div>

  <?php if ($inviteState === 'expired'): ?>
    <div class="notice" role="alert"><?= e(t('join.error.expired')) ?></div>
  <?php elseif ($inviteState === 'accepted'): ?>
    <div class="notice" role="status"><?= e(t('join.error.accepted')) ?></div>
    <div class="actions"><a class="button primary" href="/home"><?= e(t('join.goHome')) ?></a></div>
  <?php endif; ?>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="notice" role="alert" id="form-errors">
    <ul>
      <?php foreach ($errors as $error): ?><li><?= e(t($error)) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($invite === null || $inviteState === 'open'): ?>
  <?php if ($signedIn !== null && $invite !== null): ?>
    <!-- Signed in already: one tap to accept, or sign out if this is not you -->
    <p class="invite__who"><?= e(t('join.signedInAs', ['email' => $signedIn['email']])) ?></p>
    <div class="actions">
      <form method="post" action="/join/<?= e(rawurlencode($invite['invite_code'])) ?>" class="inline">
        <?= csrf_field() ?>
        <button type="submit" class="primary"><?= e(t('join.accept')) ?></button>
      </form>
      <form method="post" action="/logout" class="inline">
        <?= csrf_field() ?>
        <button type="submit" class="button-secondary"><?= e(t('join.notYou')) ?></button>
      </form>
    </div>
  <?php else: ?>
    <form method="post" action="<?= $invite !== null ? '/join/' . e(rawurlencode($invite['invite_code'])) : '/login' ?>"
          class="<?= $invite !== null ? 'invite__form' : '' ?>">
      <?= csrf_field() ?>
      <?php if ($invite !== null): ?><h2><?= e(t('join.signIn')) ?></h2><?php endif; ?>

      <label for="email"><?= e(t('join.email')) ?></label>
      <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required
             <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>>

      <label for="password"><?= e(t('join.password')) ?></label>
      <input id="password" name="password" type="password" autocomplete="current-password" required
             <?= $errors ? 'aria-invalid="true" aria-describedby="form-errors"' : '' ?>>

      <div class="actions">
        <button type="submit" class="primary"><?= e(t($invite !== null ? 'join.accept' : 'join.confirm')) ?></button>
      </div>
    </form>
  <?php endif; ?>
<?php elseif ($signedIn !== null): ?>
  <div class="actions">
    <form method="post" action="/logout" class="inline">
      <?= csrf_field() ?>
      <button type="submit" class="button-secondary"><?= e(t('join.notYou')) ?></button>
    </form>
  </div>
<?php endif; ?>

<?php if ($invite !== null): ?>
</section>
<?php endif; ?>
