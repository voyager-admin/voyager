@extends('voyager::app')
@section('page-title', __('voyager::plugins.plugins'))
@section('content')
<plugins-manager
    :available-plugins="{{ json_encode(resolve(\Voyager\Admin\Manager\Plugins::class)->getAvailablePlugins()) }}" />
@endsection
