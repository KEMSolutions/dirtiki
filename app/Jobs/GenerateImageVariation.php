<?php

namespace App\Jobs;

use App\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\ImageManager;
use Storage;

class GenerateImageVariation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image;
    public $parameters;
    public $force;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Image $image, array $parameters, bool $force = false)
    {
        $this->image = $image;
        $this->parameters = $parameters;
        $this->force = $force;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $path = $this->image->getFilePathAttribute(collect($this->parameters)->only(Image::ALLOWED_VARIATION_PARAMETERS)->toArray());

        // Avoid regenerating a variation if it already exists on disk.
        if (!$this->force && Storage::exists($path)) {
            return;
        }

        $manager = new ImageManager(['driver' => config("dirtiki.images.processor")]);
        $interventionImage = $manager->make(Storage::get($this->image->getOriginalFilePathAttribute()));

        $scale = intval(array_get($this->parameters, "scale", 1));
        $width = intval(array_get($this->parameters, "width")) * $scale;
        $height = intval(array_get($this->parameters, "height")) * $scale;

        if (!array_get($this->parameters, "fit", false) && (array_has($this->parameters, "width") || array_has($this->parameters, "height"))) {
            $interventionImage->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        } elseif (array_get($this->parameters, "fit", false) && (array_has($this->parameters, "width") || array_has($this->parameters, "height"))) {
            $interventionImage->fit($width, $height, null, array_get($this->parameters, "fit", "center"));
        }

        Storage::put($path, $interventionImage->encode());
    }
}
