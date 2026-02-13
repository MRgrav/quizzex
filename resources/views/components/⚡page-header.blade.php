<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="w-full">
    <flux:heading size="lg"><?php echo $title; ?></flux:heading>
    <?php if ($subtext): ?>
    <p class="text-sm text-gray-600 mt-2 dark:text-gray-200"><?php    echo $subtext; ?></p>
    <?php endif; ?>
</div>