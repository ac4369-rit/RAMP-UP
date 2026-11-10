<?php
$dir = \App\Translator::instance()->direction();
?>
<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(t($titleKey)) ?> · <?= e(t('app.name')) ?></title>
  <link rel="manifest" href="/manifest.json">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <a class="skip-link" href="#main"><?= e(t('a11y.skip')) ?></a>

  <div class="app">
    <div class="offline-banner" data-offline-banner role="status" hidden>
      <?= e(t('state.offlineBanner')) ?>
    </div>

    <header class="app-header">
      <span class="app-name"><?= e(t('app.name')) ?></span>
      <form method="get" class="locale-form">
        <label for="locale"><?= e(t('settings.language')) ?></label>
        <select id="locale" name="lang" data-locale-select>
          <?php foreach (\App\Translator::NAMES as $code => $label): ?>
            <option value="<?= e($code) ?>" lang="<?= e($code) ?>" <?= $code === $locale ? 'selected' : '' ?>>
              <?= e($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <noscript><button type="submit">OK</button></noscript>
      </form>
    </header>

    <main id="main" tabindex="-1">
      <?php require $view; ?>
    </main>

    <?php require __DIR__ . '/partials/nav.php'; ?>
  </div>

  <script src="/assets/js/app.js" defer></script>
</body>
</html>
