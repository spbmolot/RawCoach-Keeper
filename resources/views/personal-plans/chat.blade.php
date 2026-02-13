<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('personal-plans.show', $personalPlan) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Назад к плану
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: calc(100vh - 200px); min-height: 500px;">
            {{-- Заголовок чата --}}
            <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i data-lucide="message-circle" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Чат по плану #{{ $personalPlan->id }}</h1>
                    @if($personalPlan->nutritionist)
                        <p class="text-xs text-gray-500">Нутрициолог: {{ $personalPlan->nutritionist->name }}</p>
                    @else
                        <p class="text-xs text-gray-500">Нутрициолог будет назначен в ближайшее время</p>
                    @endif
                </div>
            </div>

            {{-- Сообщения --}}
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4" id="chatMessages">
                @forelse($messages as $msg)
                    @php $isOwn = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] {{ $isOwn ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-900' }} rounded-2xl px-4 py-3 {{ $isOwn ? 'rounded-br-md' : 'rounded-bl-md' }}">
                            @if(!$isOwn && $msg->sender)
                                <p class="text-xs {{ $isOwn ? 'text-green-100' : 'text-gray-500' }} mb-1 font-medium">{{ $msg->sender->name }}</p>
                            @endif
                            <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                            <p class="text-xs {{ $isOwn ? 'text-green-200' : 'text-gray-400' }} mt-1 text-right">
                                {{ $msg->created_at->format('d.m H:i') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="message-square" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Сообщений пока нет. Напишите первым!</p>
                    </div>
                @endforelse
            </div>

            {{-- Форма отправки --}}
            <div class="p-4 border-t border-gray-100 flex-shrink-0">
                <form id="chatForm" class="flex gap-2">
                    <input type="text" id="messageInput" placeholder="Введите сообщение..." maxlength="1000" class="flex-1 rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" autocomplete="off">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition flex-shrink-0">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Отправить</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chatMessages');
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');

            // Прокрутка вниз
            chatMessages.scrollTop = chatMessages.scrollHeight;

            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;

                // Добавляем сообщение в UI сразу
                const msgDiv = document.createElement('div');
                msgDiv.className = 'flex justify-end';
                msgDiv.innerHTML = `
                    <div class="max-w-[80%] bg-green-500 text-white rounded-2xl px-4 py-3 rounded-br-md">
                        <p class="text-sm whitespace-pre-wrap">${message.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                        <p class="text-xs text-green-200 mt-1 text-right">Отправка...</p>
                    </div>
                `;
                chatMessages.appendChild(msgDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                messageInput.value = '';

                fetch('{{ route("personal-plans.chat.send", $personalPlan) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const timeEl = msgDiv.querySelector('.text-green-200');
                        if (timeEl) {
                            const now = new Date();
                            timeEl.textContent = now.toLocaleDateString('ru', {day:'2-digit', month:'2-digit'}) + ' ' + now.toLocaleTimeString('ru', {hour:'2-digit', minute:'2-digit'});
                        }
                    }
                })
                .catch(() => {
                    const timeEl = msgDiv.querySelector('.text-green-200');
                    if (timeEl) timeEl.textContent = 'Ошибка отправки';
                });
            });
        });
    </script>
</x-app-layout>
