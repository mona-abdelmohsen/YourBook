@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge([]) }}>
        @foreach ((array) $messages as $message)
            <li class="help is-danger">
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
