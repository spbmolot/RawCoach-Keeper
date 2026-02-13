<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('ad-campaigns.show', $adCampaign) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к кампании
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Креативы: {{ $adCampaign->name }}</h1>
                <p class="text-gray-600 mt-1 text-sm">{{ $creatives->count() }} креативов</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        {{-- Форма добавления креатива --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-green-500"></i> Добавить креатив
            </h2>
            <form action="{{ route('ad-campaigns.creatives.store', $adCampaign) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Название <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="255" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                            <option value="banner" {{ old('type') === 'banner' ? 'selected' : '' }}>Баннер</option>
                            <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Текст</option>
                            <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Видео</option>
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Ссылка (URL) <span class="text-red-500">*</span></label>
                    <input type="url" name="url" id="url" value="{{ old('url') }}" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="https://" required>
                    @error('url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Текст (для текстовых креативов)</label>
                    <textarea name="content" id="content" rows="2" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Изображение (для баннеров)</label>
                        <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-1">Alt-текст</label>
                        <input type="text" name="alt_text" id="alt_text" value="{{ old('alt_text') }}" maxlength="255" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                        @error('alt_text') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-semibold transition">
                    <i data-lucide="upload" class="w-4 h-4"></i> Загрузить креатив
                </button>
            </form>
        </div>

        {{-- Список креативов --}}
        @if($creatives->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="image-off" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-gray-500">Креативов пока нет. Добавьте первый выше.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($creatives as $creative)
                    @php
                        $cStatusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                        $cStatusLabels = ['pending' => 'На модерации', 'approved' => 'Одобрен', 'rejected' => 'Отклонён'];
                        $typeIcons = ['banner' => 'image', 'text' => 'type', 'video' => 'video'];
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $typeIcons[$creative->type] ?? 'file' }}" class="w-6 h-6 text-indigo-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-medium text-gray-900 truncate">{{ $creative->name }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $cStatusColors[$creative->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $cStatusLabels[$creative->status] ?? $creative->status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span>{{ ucfirst($creative->type) }}</span>
                                <span>{{ $creative->created_at->format('d.m.Y') }}</span>
                                @if($creative->url)
                                    <a href="{{ $creative->url }}" target="_blank" class="text-indigo-600 hover:underline truncate max-w-[200px]">{{ $creative->url }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
