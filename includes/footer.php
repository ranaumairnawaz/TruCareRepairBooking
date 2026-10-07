<?php /** @var array $settings */ ?>
<footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-left text-xs mb-8">
      <div class="space-y-3">
        <div class="text-white text-base font-bold font-heading"><?= e($settings['logo']) ?></div>
        <p class="text-gray-400 leading-relaxed"><?= e($settings['footerDescription']) ?></p>
      </div>
      <div>
        <h4 class="text-white font-bold mb-3 uppercase tracking-wider">Quick Views</h4>
        <ul class="space-y-2">
          <li><a href="<?= e(url()) ?>" class="hover:text-white transition">Home Dashboard</a></li>
          <li><a href="<?= e(url('about.php')) ?>" class="hover:text-white transition">About Our History</a></li>
          <li><a href="<?= e(url('contact.php')) ?>" class="hover:text-white transition">Contact Details</a></li>
          <li><a href="<?= e(url('booking.php')) ?>" class="hover:text-white transition">Book Repair Specialist</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-white font-bold mb-3 uppercase tracking-wider">Services offered</h4>
        <ul class="space-y-1.5 font-medium">
          <?php foreach (array_slice($settings['services'] ?? [], 0, 4) as $ser): ?>
          <li class="text-gray-500">• <?= e($ser['name']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-white font-bold mb-3 uppercase tracking-wider">Contact Info</h4>
        <ul class="space-y-2 text-gray-400">
          <li>📞 <?= e($settings['contact']['phone']) ?></li>
          <li>📧 <?= e($settings['contact']['email']) ?></li>
          <li class="leading-relaxed">📍 <?= e($settings['contact']['address']) ?></li>
          <li>🕒 <?= e($settings['contact']['hours']) ?></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-[11px] text-gray-500">&copy; <?= date('Y') ?> <?= e($settings['platformName']) ?>. All rights reserved.</p>
      <div class="flex gap-4 text-[10px] text-gray-500 font-semibold">
        <span>90-Day Parts & Labor Warranty Included</span>
      </div>
    </div>
  </div>
</footer>

<script src="https://unpkg.com/lucide@0.546.0/dist/umd/lucide.min.js"></script>
<script src="<?= e(asset('js/main.js')) ?>"></script>
<?php if (!empty($extraScripts)): foreach ((array)$extraScripts as $script): ?>
<script src="<?= e(asset('js/' . $script)) ?>"></script>
<?php endforeach; endif; ?>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
