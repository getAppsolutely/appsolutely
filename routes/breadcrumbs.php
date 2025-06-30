<?php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;
// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('page', function (BreadcrumbTrail $trail, $page) {
    $trail->parent('home');

    if (! empty($page['ancestors']) && is_array($page['ancestors'])) {
        foreach ($page['ancestors'] as $ancestor) {
            $trail->push($ancestor['title'], route('pages.show', $ancestor['slug']));
        }
    }

    $trail->push($page['title'], route('pages.show', $page['slug']));
});
