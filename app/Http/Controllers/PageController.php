<?php

namespace App\Http\Controllers;

use App\Page;
use App\PageRedirection;
use Auth;
use Illuminate\Http\Request;
use Route;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!config("dirtiki.allow_anonymous.views")) {
            $this->middleware('auth');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!policy(Page::class)->store(Auth::user()), 403);
        return view('pages.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $page)
    {
        $slug = $page;
        $page = Page::with("body")->where("slug", $slug)->first();
        if (!$page && $pageRedirection = PageRedirection::where("slug", $slug)->first()) {
            return redirect()->route(Route::currentRouteName(), [$pageRedirection->page]);
        } elseif (!$page) {
            abort(404);
        }
        abort_if(!policy(Page::class)->view(Auth::user(), $page), 403);
        return view('pages.show', ['page' => $page]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        abort_if(!policy(Page::class)->update(Auth::user(), $page), 403);
        return view('pages.edit', ['page' => $page]);
    }

}
