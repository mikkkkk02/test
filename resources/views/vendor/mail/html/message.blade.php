@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            CSG e-Workflow
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    @isset($approve)
    @isset($disapprove)
        @slot('approval')
        @endslot
    @endisset
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            &copy; {{ date('Y') }} SN Aboitiz Power Group. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
