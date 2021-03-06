<?php

namespace App\Http\Controllers;

use App\Image;
use App\Jobs\GenerateImageVariation;
use Auth;
use Illuminate\Http\Request;
use Storage;

class ImageController extends Controller
{

    public function getShow(Request $request, Image $image)
    {
        abort_if(!policy(Image::class)->view(Auth::user(), $image), 403);

        $path = $image->getFilePathAttribute($request->only(Image::ALLOWED_VARIATION_PARAMETERS));
        if ($image->type !== "image/svg+xml") {

            // Compare requested parameters with defined presets. This way we avoid cluttering
            // our storage with every single imaginable image variation.
            $requestedVariationCollection = collect($request->only(Image::ALLOWED_VARIATION_PARAMETERS))->forget("scale");
            collect(config("dirtiki.images.presets"))->map(function ($preset) use ($requestedVariationCollection) {
                return collect($preset)->diff($requestedVariationCollection);
            })->reject(function ($variation) {
                return $variation->count();
            })->pipe(function ($remaining) {
                if (!$remaining->count()) {
                    abort(404, "Requested variation is not a registred preset.");
                }
            });

            if (!Storage::exists($path)) {
                $this->dispatchNow(new GenerateImageVariation($image, $request->only(Image::ALLOWED_VARIATION_PARAMETERS)));
            }

        }

        return response(Storage::get($path))
            ->withHeaders([
                'Content-Type' => $image->type,
            ]);
    }

}
