@extends('voyager::app')
@section('page-title', __('voyager::generic.media'))
@section('content')
<card title="{{ __('voyager::generic.media') }}" icon="photograph">
    <div class="mt-4">
        <media-manager
            :upload-url="route('voyager.media.upload')"
            :list-url="route('voyager.media.list')"
            :drag-text="__('voyager::media.drag_files_here')"
            :drop-text="__('voyager::media.drop_files')"
            :allowed-mime-types="{{ json_encode(Voyager::setting('media.mime-types', [])) }}"
            ref="media">
        </media-manager>
    </div>
</card>
@endsection
