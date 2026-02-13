@blaze

@props([
    'size' => null,
])
@php
    $classes = \Flux\Flux::classes()
        ->add('[:where(&)]:bg-white dark:[:where(&)]:bg-white')
        ->add('border border-zinc-200 ')
        ->add(match ($size) {
            default => '[:where(&)]:p-6 [:where(&)]:rounded-xl',
            'sm' => '[:where(&)]:p-4 [:where(&)]:rounded-lg',
        })
    ;
@endphp

<div {{ $attributes->class($classes) }} data-flux-card>
    {{ $slot }}
</div>
