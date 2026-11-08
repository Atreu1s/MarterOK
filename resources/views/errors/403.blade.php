@extends('errors.layout')
@section('code', '403')
@section('heading', 'Сюда нужен другой доступ.')
@php
    // Текст из Response::deny() в политиках и шлюзах (лаба 7).
    $reason = isset($exception) ? $exception->getMessage() : '';
    if ($reason === '' || $reason === 'This action is unauthorized.') {
        $reason = 'Эта страница доступна владельцу заявки или модератору. Проверьте, под каким аккаунтом вы вошли.';
    }
@endphp
@section('message', $reason)
