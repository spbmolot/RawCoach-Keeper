<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('ad-campaigns.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к кампаниям
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Новая рекламная кампания</h1>
                <p class="text-gray-600 mt-1 text-sm">После создания кампания будет отправлена на модерацию</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            <form action="{{ route('ad-campaigns.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Название кампании <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="255" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                    <textarea name="description" id="description" rows="3" maxlength="1000" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">Общий бюджет (₽) <span class="text-red-500">*</span></label>
                        <input type="number" name="budget" id="budget" value="{{ old('budget') }}" min="0" step="0.01" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="daily_budget" class="block text-sm font-medium text-gray-700 mb-1">Дневной бюджет (₽)</label>
                        <input type="number" name="daily_budget" id="daily_budget" value="{{ old('daily_budget') }}" min="0" step="0.01" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                        @error('daily_budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Дата начала <span class="text-red-500">*</span></label>
                        <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">Дата окончания <span class="text-red-500">*</span></label>
                        <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                        @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if($placements->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Размещения <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @foreach($placements as $placement)
                                <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-green-300 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                    <input type="checkbox" name="placement_ids[]" value="{{ $placement->id }}" {{ in_array($placement->id, old('placement_ids', [])) ? 'checked' : '' }} class="rounded text-green-500 focus:ring-green-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">{{ $placement->name }}</span>
                                        @if($placement->description)
                                            <p class="text-xs text-gray-500">{{ $placement->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('placement_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Целевая аудитория (JSON)</label>
                    <textarea name="target_audience" id="target_audience" rows="3" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm font-mono text-xs" placeholder='{"age_from": 25, "age_to": 45, "gender": "female"}'>{{ old('target_audience') }}</textarea>
                    @error('target_audience') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition shadow-lg shadow-green-500/25">
                        <i data-lucide="send" class="w-4 h-4"></i> Создать кампанию
                    </button>
                    <a href="{{ route('ad-campaigns.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
