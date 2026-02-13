@extends('emails.layout')

@section('title', 'Обратная связь')

@section('content')
    <h2>Новое сообщение с сайта</h2>
    
    <div class="info-box">
        <h3>Данные отправителя</h3>
        <p><strong>Имя:</strong> {{ $contactName }}</p>
        <p><strong>Email:</strong> <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
        <p><strong>Тема:</strong> {{ $contactSubject }}</p>
    </div>
    
    <h3>Сообщение:</h3>
    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 16px 0;">
        <p style="white-space: pre-wrap;">{{ $contactMessage }}</p>
    </div>
    
    <div class="divider"></div>
    
    <p style="color: #6b7280; font-size: 14px;">Вы можете ответить на это письмо — ответ уйдёт напрямую отправителю ({{ $contactEmail }}).</p>
@endsection
