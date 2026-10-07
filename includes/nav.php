<?php
/** @var array $settings */
/** @var string $currentPage */
$currentPage = $currentPage ?? 'home';
[$logoEmoji, $logoText] = splitLogo($settings['logo'] ?? $settings['platformName']);
$isAdmin = !empty($_SESSION['admin_token']);
?>
<header class="sticky top-0 z-40 w-full border-b border-gray-100 bg-white/95 backdrop-blur-md">
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <a href="<?= e(url()) ?>" id="site-logo" class="flex items-center gap-2 text-xl font-bold tracking-tight text-[var(--secondary-color)] hover:opacity-90 select-none" title="Double-click to access administrative console">
      <span class="text-[var(--primary-color)]"><?= e($logoEmoji) ?></span>
      <span><?= e($logoText ?: $settings['platformName']) ?></span>
    </a>

    <div class="flex items-center gap-6">
      <nav class="hidden md:flex items-center gap-6">
        <?php
        $navItems = [
            'home' => ['label' => 'Home', 'href' => url()],
            'services' => ['label' => 'Services', 'href' => url('services.php')],
            'about' => ['label' => 'About Us', 'href' => url('about.php')],
            'contact' => ['label' => 'Contact', 'href' => url('contact.php')],
        ];
        foreach ($navItems as $id => $item):
            $active = $currentPage === $id;
        ?>
        <a href="<?= e($item['href']) ?>" class="text-sm font-medium transition-all <?= $active ? 'text-[var(--primary-color)] font-bold' : 'text-gray-500 hover:text-[var(--secondary-color)]' ?>">
          <?= e($item['label']) ?>
        </a>
        <?php endforeach; ?>
      </nav>

      <div class="flex items-center gap-3">
        <a href="<?= e(url('booking.php')) ?>" class="flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition-all <?= $currentPage === 'booking' ? 'bg-primary-light text-[var(--primary-color)] border border-primary-border/40' : 'bg-[var(--primary-color)] text-white hover:opacity-95 shadow-sm' ?>">
          <?= renderIcon('calendar', 'w-3.5 h-3.5') ?>
          Book Repair
        </a>

        <?php if ($isAdmin): ?>
        <a href="<?= e(url('admin/')) ?>" class="flex items-center gap-1 rounded-xl px-3 py-2 text-xs font-bold transition-all <?= $currentPage === 'admin' ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-900' ?>">
          <?= renderIcon('lock', 'w-3.5 h-3.5 text-green-600') ?>
          <span class="hidden sm:inline">Admin Panel</span>
        </a>
        <button type="button" id="admin-logout-btn" class="hidden sm:block text-xs font-semibold text-red-500 hover:text-red-700 px-2 py-1 bg-red-50 hover:bg-red-100/70 rounded-lg cursor-pointer">Log Out</button>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="flex md:hidden border-t border-gray-100 bg-white px-4 py-2 justify-around text-[11px] font-semibold text-gray-500">
    <a href="<?= e(url()) ?>" class="<?= $currentPage === 'home' ? 'text-[var(--primary-color)]' : '' ?>">Home</a>
    <a href="<?= e(url('services.php')) ?>" class="<?= $currentPage === 'services' ? 'text-[var(--primary-color)]' : '' ?>">Services</a>
    <a href="<?= e(url('about.php')) ?>" class="<?= $currentPage === 'about' ? 'text-[var(--primary-color)]' : '' ?>">About</a>
    <a href="<?= e(url('contact.php')) ?>" class="<?= $currentPage === 'contact' ? 'text-[var(--primary-color)]' : '' ?>">Contact</a>
  </div>
</header>
