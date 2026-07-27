@props([
    'showSearch' => true,
    'showDateFilter' => true,
    'title' => '',
])

@livewire('global-header', [
    'showSearch' => $showSearch,
    'showDateFilter' => $showDateFilter,
    'title' => $title,
])
