@props(['user', 'size' => 'w-10 h-10', 'text' => 'text-base'])

@if($user->avatar)
    <img src="{{ asset('storage/' . $user->avatar) }}" alt=""
         class="{{ $size }} rounded-full object-cover flex-shrink-0 ring-2 ring-white">
@else
    <div class="{{ $size }} rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold flex-shrink-0 {{ $text }}">
        {{ mb_substr($user->name, 0, 1) }}
    </div>
@endif
