{{--
    ⚠️ Vue inutilisée depuis la mise en place des pages éditables.
    /about-us est désormais rendu par resources/views/pages/show.blade.php
    et son contenu se modifie depuis l'admin : Espace admin → Pages du site.
    Fichier conservé à titre de référence uniquement.
--}}
@extends('layouts.app')

@section('content')
<div class="bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-4">
            About Us
        </h1>

        <div class="prose prose-lg text-gray-700 leading-relaxed">
            <p class="mb-6">
                Al Bashawari Optical is known as the first and oldest optical name in the Kingdom,
                the name is known from our grandparents and continues to 3 generations for more
                than 100 years, we expanded in all regions of the Kingdom but specially we are
                the Eastern team, we started our first branch in Dammam until we reached Jazan
                recentally we have more than 13 branches, we offer you exclusive brands in the
                Kingdom such as Mango, Lamborghini, Pull & Bear, Fisher Price, Barbie and more,
                our goal is to give you a pleasent shopping online experience and deliver you
                our products to all parts and cities of the Kingdom.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-gray-50 rounded-lg text-center">
                <span class="block text-2xl font-bold text-indigo-600">100+</span>
                <span class="text-sm text-gray-500 uppercase">Years of Experience</span>
            </div>
            <div class="p-6 bg-gray-50 rounded-lg text-center">
                <span class="block text-2xl font-bold text-indigo-600">13+</span>
                <span class="text-sm text-gray-500 uppercase">Branches</span>
            </div>
            <div class="p-6 bg-gray-50 rounded-lg text-center">
                <span class="block text-2xl font-bold text-indigo-600">3</span>
                <span class="text-sm text-gray-500 uppercase">Generations</span>
            </div>
        </div>
    </div>
</div>
@endsection
