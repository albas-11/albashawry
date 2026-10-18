
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($brands as $brand)
    <url>
        <loc>{{ url('/brands/' . $brand->slug) }}</loc>
        @if ($brand->updated_at)
            <lastmod>{{ $brand->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
