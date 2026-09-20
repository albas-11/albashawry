<!-- Afficher le nom du site -->
{{ site_name() }}

<!-- Afficher le logo -->
<img src="{{ site_logo() }}" alt="Logo">

<!-- Favicon -->
<link rel="icon" href="{{ site_favicon() }}">

<!-- Email admin -->
{{ settings('admin_email') }}

<!-- Téléphone -->
{{ settings()->phone }}

<!-- Meta Tags SEO -->
<x-meta-tags
    :title="'Page Titre'"
    :description="'Description de la page'"
/>
