@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" class="relative z-0">
    @php
        $textareaID = 'tiny-editor-' . str_replace(['.', '#', '$'], '-', $getId()) . '-' . rand();
    @endphp

    {{-- 1. Bungkus dengan x-data untuk state Alpine --}}
    <div x-data>
        {{-- 2. Tombol Keren yang Dilihat Pengguna --}}
        <button
            type="button"
            @click="$refs.docxInput.click()"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Impor dari .docx
        </button>

        {{-- 3. Input File Asli yang Disembunyikan --}}
        <input
            type="file"
            id="docx_importer_{{ $getId() }}"
            wire:model.live="docxFile"
            x-ref="docxInput"
            class="hidden"
        >

        {{-- Pesan Loading (tetap sama) --}}
        <div wire:loading wire:target="docxFile" class="mt-2 text-sm text-gray-500">
            <i class="fas fa-spinner fa-spin"></i> Mengunggah dan memproses...
        </div>
        @error('docxFile') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
    </div>

    {{-- Input File Asli yang Disembunyikan --}}
    <input
        type="file"
        id="docx_importer_{{ $getId() }}"
        wire:model.live="docxFile"
        x-ref="docxInput"
        class="hidden"
    >


    <div wire:ignore x-ignore ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tinyeditor', 'amidesfahani/filament-tinyeditor') }}"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('tiny-css', package: 'amidesfahani/filament-tinyeditor'))]"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc($getLanguageId(), package: 'amidesfahani/filament-tinyeditor'))]"
        x-data="tinyeditor({
            state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')", isOptimisticallyLive: false) }},
            statePath: '{{ $statePath }}',
            selector: '#{{ $textareaID }}',
            plugins: '{{ $getPlugins() }}',
            external_plugins: {{ $getExternalPlugins() }},
            toolbar: '{{ $getToolbar() }}',
            @if(!$getTextPattern())
                text_patterns: @js($getTextPattern()),
            @endif
            language: '{{ $getInterfaceLanguage() }}',
            language_url: '{{ $getLanguageURL($getInterfaceLanguage()) }}',
            directionality: '{{ $getDirection() }}',
            @if ($getHeight())
            height: @js($getHeight()),
            @endif
            @if ($getMaxHeight())
            max_height: @js($getMaxHeight()),
            @endif
            @if ($getMinHeight())
            min_height: @js($getMinHeight()),
            @endif
            @if ($getWidth())
            width: @js($getWidth()),
            @endif
            @if ($getTinyMaxWidth())
            max_width: @js($getTinyMaxWidth()),
            @endif
            @if ($getMinWidth())
            min_width: @js($getMinWidth()),
            @endif
            resize: @js($getResize()),
            @if (!filament()->hasDarkModeForced() && $darkMode() == 'media') skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
			content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
			@elseif(!filament()->hasDarkModeForced() && $darkMode() == 'class')
			skin: (document.querySelector('html').getAttribute('class').includes('dark') ? 'oxide-dark' : 'oxide'),
			content_css: (document.querySelector('html').getAttribute('class').includes('dark') ? 'dark' : 'default'),
			@elseif(filament()->hasDarkModeForced() || $darkMode() == 'force')
			skin: 'oxide-dark',
			content_css: 'dark',
			@elseif(!filament()->hasDarkModeForced() && $darkMode() == false)
			skin: 'oxide',
			content_css: 'default',
			@elseif(!filament()->hasDarkModeForced() && $darkMode() == 'custom')
			skin: '{{ $skinsUI() }}',
			content_css: '{{ $skinsContent() }}',
			@else
			skin: ((localStorage.getItem('theme') ?? 'system') == 'dark' || (localStorage.getItem('theme') === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) ? 'oxide-dark' : 'oxide',
			content_css: ((localStorage.getItem('theme') ?? 'system') == 'dark' || (localStorage.getItem('theme') === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) ? 'dark' : 'default',
            @endif
            toolbar_sticky: {{ $getToolbarSticky() ? 'true' : 'false' }},
            toolbar_sticky_offset: {{ $getToolbarStickyOffset() }},
            toolbar_mode: '{{ $getToolbarMode() }}',
            toolbar_location: '{{ $getToolbarLocation() }}',
            inline: {{ $getInlineOption() ? 'true' : 'false' }},
            toolbar_persist: {{ $getToolbarPersist() ? 'true' : 'false' }},
            menubar: {{ $getShowMenuBar() ? 'true' : 'false' }},
            relative_urls: {{ $getRelativeUrls() ? 'true' : 'false' }},
            remove_script_host: {{ $getRemoveScriptHost() ? 'true' : 'false' }},
            convert_urls: {{ $getConvertUrls() ? 'true' : 'false' }},
            font_size_formats: '{{ $getFontSizes() }}',
            fontfamily: '{{ $getFontFamilies() }}',
            setup: null,
            disabled: @js($isDisabled),
            locale: '{{ app()->getLocale() }}',
            placeholder: @js($getPlaceholder()),
            image_list: {!! $getImageList() !!},
            @if ($getImagesUploadUrl !== false)
            images_upload_url: @js($getImagesUploadUrl()),
            @endif
            image_advtab: @js($imageAdvtab()),
            image_description: @js($getImageDescription()),
            image_class_list: @js($getImageClassList()),
            license_key: '{{ $getLicenseKey() }}',
            custom_configs: {{ $getCustomConfigs() }},
            {{-- removeImagesEventCallback: (img) => {
                if (confirm('{{ __('Are you sure you want to remove this image?') }}')) {
                    console.log(img)
                }
            }, --}}
        })">
        @if ($isDisabled())
            <div x-html="state" @style(['max-height: ' . $getPreviewMaxHeight() . 'px' => $getPreviewMaxHeight() > 0, 'min-height: ' . $getPreviewMinHeight() . 'px' => $getPreviewMinHeight() > 0])
                class="block w-full p-3 overflow-y-auto prose transition duration-75 bg-white border border-gray-300 rounded-lg shadow-sm max-w-none opacity-70 dark:prose-invert dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
        @else
            <input id="{{ $textareaID }}" type="hidden" x-ref="tinymce" placeholder="{{ $getPlaceholder() }}">
        @endif
    </div>
</x-dynamic-component>

@pushOnce('scripts')
    <script>
        // window.addEventListener('beforeunload', (event) => {
        //     if (tinymce.activeEditor.isDirty()) {
        //         event.preventDefault();
        // 		// Included for legacy support, e.g. Chrome/Edge < 119
        // 		event.returnValue = '{{ __('Are you sure you want to leave?') }}';
        //     }
        // });
    </script>
@endPushOnce
