@extends('voyager::app')
@section('page-title', __('voyager::generic.dashboard'))
@section('content')
    <div class="flex">
        @forelse (Voyager::getWidgets() as $widget)
        <card
            :show-header="{{ $widget->title || $widget->icon ? 'true' : 'false' }}"
            title="{{ $widget->title }}"
            icon="{{ $widget->icon }}"
            class="{{ $widget->width }}"
        >
            <div>
                {!! $widget->view->render() !!}
            </div>
        </card>
        @empty
        <card title="Welcome to Voyager II" icon="helm" :icon-size="8" class="w-full">
            <div>Voyager 2 has been re-built using Laravel and VueJS. There are a lot of other cool things about version 2</div>
        </card>
        @endforelse
    </div>
@endsection