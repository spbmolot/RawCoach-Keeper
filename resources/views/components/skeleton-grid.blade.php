@props(['count' => 6, 'cols' => 3, 'type' => 'card'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $cols }} gap-6">
    @for($i = 0; $i < $count; $i++)
        <x-skeleton-card :type="$type" />
    @endfor
</div>
