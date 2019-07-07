<?php

namespace App\Http\Controllers;

use App\Services\SiteMap;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use App\Services\PostService;
use App\Services\RssFeed;

class BlogController extends Controller
{
    public function index(Request $request){
        // $posts = Post::where('published_at', '<=',Carbon::now())
        //         ->orderBy('published_at','desc')
        //         ->paginate(config('blog.posts_pre_page'));
        $tag = $request->get('tag');
        $postService = new PostService($tag);
        $data = $postService->lists();
        $layout = $tag ? Tag::layout($tag) : 'blog.layouts.index';

        return view($layout,$data);
    }
    public function showPost($slug, Request $request){
        // $post = Post::where('slug', $slug)->firstOrFail();
        $post = Post::with('tags')->where('slug', $slug)->firstOrFail();
        $tag = $request->get('tag');
        if($tag){
            $tag = Tag::where('tag', $tag)->firstOrFail();
        }
        return view($post->layout, compact('post', 'tag'));
    }

    public function rss(RssFeed $feed)
    {
        $rss = $feed->getRSS();

        return response($rss)
            ->header('Content-type', 'application/rss+xml');
    }

    // 站点地图
    public function siteMap(SiteMap $siteMap)
    {
        $map = $siteMap->getSiteMap();

        return response($map)
        ->header('Content-type', 'text/xml');
    }

}
