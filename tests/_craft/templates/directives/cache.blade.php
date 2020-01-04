<div>
    @cache(['global' => true, 'key' => $key])
        {{ $content }}
    @endcache
</div>