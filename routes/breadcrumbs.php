<?php


use Diglactic\Breadcrumbs\Breadcrumbs;


use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', env('MAIN_URL'));
});
Breadcrumbs::for('artworks', function (BreadcrumbTrail $trail) {
    $trail->push('Objects and Artworks', env('APP_URL'));
});


Breadcrumbs::for('record', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Object');
});

Breadcrumbs::for('image.single', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Single image');
});

Breadcrumbs::for('sketchfab', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('3D image views');
});

Breadcrumbs::for('iiif', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('IIIF image view');
});

Breadcrumbs::for('image.mirador', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Mirador IIIF image view');
});

Breadcrumbs::for('images.multiple', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('All images attached');
});

Breadcrumbs::for('publication.record', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Publication details');
});


Breadcrumbs::for('exhibition.record', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Exhibition details');
});

Breadcrumbs::for('terminology', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Terminology details');
});

Breadcrumbs::for('agent', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Agent details');
});
Breadcrumbs::for('department', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Department Details');
});

Breadcrumbs::for('api.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Using our API');
});



