<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::slug($slug)->first();

        if (! $page) {
            throw new NotFoundHttpException();
        }

        // Une page désactivée reste visible pour l'admin (prévisualisation)
        if (! $page->is_active && ! (auth()->check() && auth()->user()->hasRole('Admin'))) {
            throw new NotFoundHttpException();
        }

        return view('pages.show', compact('page'));
    }
}
