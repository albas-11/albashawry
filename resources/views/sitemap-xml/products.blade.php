
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($products as $product)
    <url>
        <loc>{{ $product->canonical_url ?: $product->public_url }}</loc>
        @if ($product->updated_at)
            <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
