@props(['type' => 'card'])

@if($type === 'card')
{{-- Скелетон для карточки рецепта/меню --}}
<div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 animate-pulse">
    <div class="h-48 bg-gray-200"></div>
    <div class="p-6">
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
        <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
        <div class="h-3 bg-gray-200 rounded w-2/3 mb-4"></div>
        <div class="flex gap-2">
            <div class="h-6 bg-gray-200 rounded-full w-16"></div>
            <div class="h-6 bg-gray-200 rounded-full w-20"></div>
        </div>
    </div>
</div>

@elseif($type === 'list-item')
{{-- Скелетон для элемента списка --}}
<div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 animate-pulse">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-gray-200 rounded-xl flex-shrink-0"></div>
        <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
        <div class="h-8 w-8 bg-gray-200 rounded-lg"></div>
    </div>
</div>

@elseif($type === 'text')
{{-- Скелетон для текста --}}
<div class="animate-pulse space-y-3">
    <div class="h-4 bg-gray-200 rounded w-full"></div>
    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
    <div class="h-4 bg-gray-200 rounded w-4/6"></div>
</div>

@elseif($type === 'avatar')
{{-- Скелетон для аватара с текстом --}}
<div class="flex items-center gap-3 animate-pulse">
    <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
    <div class="flex-1">
        <div class="h-4 bg-gray-200 rounded w-24 mb-1"></div>
        <div class="h-3 bg-gray-200 rounded w-16"></div>
    </div>
</div>

@elseif($type === 'stats')
{{-- Скелетон для статистики --}}
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 animate-pulse">
    <div class="h-3 bg-gray-200 rounded w-20 mb-3"></div>
    <div class="h-8 bg-gray-200 rounded w-16 mb-2"></div>
    <div class="h-2 bg-gray-200 rounded w-24"></div>
</div>

@elseif($type === 'table-row')
{{-- Скелетон для строки таблицы --}}
<tr class="animate-pulse">
    <td class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-full"></div></td>
    <td class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-3/4"></div></td>
    <td class="px-4 py-3"><div class="h-4 bg-gray-200 rounded w-1/2"></div></td>
    <td class="px-4 py-3"><div class="h-6 bg-gray-200 rounded-full w-16"></div></td>
</tr>
@endif
