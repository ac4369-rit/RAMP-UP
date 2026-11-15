<?php
//The variables used are created by the controller
$dir     = \App\Translator::instance()->direction();
$current = \App\Auth::user();
?>
<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#1d4a6e">
  <title><?= e(t($titleKey)) ?> · <?= e(t('app.name')) ?></title>
  <link rel="manifest" href="/manifest.json">
  <link rel="icon" href="/assets/img/icon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
  <link rel="stylesheet" href="/assets/css/style.css?v=2">
</head>
<body>
  <a class="skip-link" href="#main"><?= e(t('a11y.skip')) ?></a>

  <div class="app<?= $current !== null ? ' app--signed-in' : '' ?>">
    <div class="offline-banner" data-offline-banner role="status" hidden>
      <?= e(t('state.offlineBanner')) ?>
    </div>

    <header class="app-header">
      <a class="app-name" href="/home">
        <span class="app-logo" aria-hidden="true">R</span>
        <?= e(t('app.name')) ?>
      </a>
      <div class="app-header__tools">
        <form method="get" class="locale-form">
          <label for="locale" class="visually-hidden"><?= e(t('settings.language')) ?></label>
          <svg class="locale-form__icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <select id="locale" name="lang" data-locale-select>
            <?php foreach (\App\Translator::NAMES as $code => $label): ?>
              <option value="<?= e($code) ?>" lang="<?= e($code) ?>" <?= $code === $locale ? 'selected' : '' ?>>
                <?= e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <noscript><button type="submit">OK</button></noscript>
        </form>
        <?php if ($current !== null): ?>
          <a class="header-avatar" href="/profile" title="<?= e(t('sub.profile')) ?>">
            <span aria-hidden="true"><?= e(mb_strtoupper(mb_substr($current['name'], 0, 1))) ?></span>
            <span class="visually-hidden"><?= e(t('sub.profile')) ?></span>
          </a>
        <?php endif; ?>
      </div>
    </header>

    <main id="main" tabindex="-1">
      <?php require $view; ?>
    </main>

    <?php require __DIR__ . '/partials/nav.php'; ?>
  </div>

  <script src="/assets/js/app.js" defer></script>
</body>
</html>
