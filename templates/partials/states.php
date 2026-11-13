<?php
/**
 * Shared empty / error message box.
 * Expects: $state ('empty' | 'error'), optional $messageKey (translation key),
 * optional $retryHref (link for the "Try again" button on errors).
 */
$state      = $state ?? 'empty';
$messageKey = $messageKey ?? ($state === 'error' ? 'state.error' : 'state.empty');
?>
<div class="state state--<?= e($state) ?>" role="<?= $state === 'error' ? 'alert' : 'status' ?>">
  <p><?= e(t($messageKey)) ?></p>
  <?php if ($state === 'error' && !empty($retryHref)): ?>
    <a class="button" href="<?= e($retryHref) ?>"><?= e(t('state.retry')) ?></a>
  <?php endif; ?>
</div>
