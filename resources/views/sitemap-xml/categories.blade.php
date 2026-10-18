
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($categories as $category)
    <url>
        <loc>{{ url('/products/category/' . $category->slug) }}</loc>
        @if ($category->updated_at)
            <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
