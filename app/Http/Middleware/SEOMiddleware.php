<?php

namespace App\Http\Middleware;

use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Closure;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Litlife\Url\Url;

class SEOMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		SEOMeta::setDescription(__('seo.description'));
		SEOMeta::addKeyword(__('seo.keywords'));

		$url = Url::fromString($request->fullUrl());

		$page = intval($url->getQueryParameter('page'));

		if ($page < 2) {
			$url = (string)$url->withoutQueryParameter('page');
		} elseif ($page > 1) {
			$url = (string)$url->withQueryParameter('page', $page);
		} else {
			$url = (string)$url;
		}

		$title = ltrim(Breadcrumbs::pageTitle(), ':: ');

		OpenGraph::addProperty('url', $url);
		OpenGraph::setTitle($title);
		OpenGraph::setType('website');
		OpenGraph::addImage(Url::fromString(config('app.url') . '/img/brand.png'));

		TwitterCard::setUrl($url);
		SEOMeta::setCanonical($url);

		return $next($request);
	}

	public function terminate($request, $response)
	{
		Facade::clearResolvedInstance('seotools.opengraph');
		Facade::clearResolvedInstance('seotools.metatags');
		Facade::clearResolvedInstance('seotools');
		Facade::clearResolvedInstance('seotools.twitter');
	}
}
