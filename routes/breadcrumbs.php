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
    $trail->push('Object or Artwork');
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

Breadcrumbs::for('publications', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Publications');
});

Breadcrumbs::for('publication.record', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Publications', route('publications'));
    $trail->push('Publication details');
});

Breadcrumbs::for('exhibitions', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Exhibitions');
});

Breadcrumbs::for('exhibition.record', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Exhibitions', route('exhibitions'));
    $trail->push('Exhibition details');
});

Breadcrumbs::for('terminologies', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Terminologies');
});

Breadcrumbs::for('places', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Places');
});

Breadcrumbs::for('periods', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Periods');
});

Breadcrumbs::for('terminology', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Terminologies', route('terminologies'));
    $trail->push('Terminology details');
});

Breadcrumbs::for('agents', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Agents');
});

Breadcrumbs::for('agent', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Agents', route('agents'));
    $trail->push('Agent details');
});
Breadcrumbs::for('department', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Departments', route('departments'));
    $trail->push('Department Details');
});
Breadcrumbs::for('departments', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Departments');
});
Breadcrumbs::for('api.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Using our API');
});

Breadcrumbs::for('results', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Search results');
});

Breadcrumbs::for('search', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->parent('artworks');
    $trail->push('Search our collection');
});



