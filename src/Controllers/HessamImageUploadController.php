<?php

namespace HessamDev\Hessam\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use HessamDev\Hessam\Middleware\UserCanManageBlogPosts;
use HessamDev\Hessam\Models\HessamUploadedPhoto;
use File;
use HessamDev\Hessam\Requests\UploadImageRequest;
use HessamDev\Hessam\Traits\UploadFileTrait;

/**
 * Class HessamAdminController
 * @package HessamDev\Hessam\Controllers
 */
class HessamImageUploadController extends Controller
{

    use UploadFileTrait;

    /**
     * HessamAdminController constructor.
     */
    public function __construct()
    {
        $this->middleware(UserCanManageBlogPosts::class);

        if (!is_array(config("hessam"))) {
            throw new \RuntimeException('The config/hessam.php does not exist. Publish the vendor files for the Hessam package by running the php artisan publish:vendor command');
        }


        if (!config("hessam.image_upload_enabled")) {
            throw new \RuntimeException("The hessam.php config option has not enabled image uploading");
        }


    }

    /**
     * Show the main listing of uploaded images
     * @return mixed
     */


    public function index()
    {
        return view("hessam_admin::imageupload.index", ['uploaded_photos' => HessamUploadedPhoto::orderBy("id", "desc")->paginate(10)]);
    }

    /**
     * show the form for uploading a new image
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view("hessam_admin::imageupload.create", []);
    }

    /**
     * Save a new uploaded image
     *
     * @param UploadImageRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(UploadImageRequest $request)
    {
        $processed_images = $this->processUploadedImages($request);

        return view("hessam_admin::imageupload.uploaded", ['images' => $processed_images]);
    }

    /**
     * Process any uploaded images (for featured image)
     *
     * @param UploadImageRequest $request
     *
     * @return array returns an array of details about each file resized.
     * @throws \Exception
     * @todo - This class was added after the other main features, so this duplicates some code from the main blog post admin controller (HessamAdminController). For next full release this should be tided up.
     */
    protected function processUploadedImages(UploadImageRequest $request)
    {
        $this->increaseMemoryLimit();
        $photo = $request->file('upload');

        // to save in db later
        $uploaded_image_details = [];

        $sizes_to_upload = $request->get("sizes_to_upload");

        // now upload a full size - this is a special case, not in the config file. We only store full size images in this class, not as part of the featured blog image uploads.
        if (isset($sizes_to_upload['hessam_full_size']) && $sizes_to_upload['hessam_full_size'] === 'true') {

            $uploaded_image_details['hessam_full_size'] = $this->UploadAndResize(null, $request->get("image_title"), 'fullsize', $photo);

        }

        foreach ((array)config('hessam.image_sizes') as $size => $image_size_details) {

            if (!isset($sizes_to_upload[$size]) || !$sizes_to_upload[$size] || !$image_size_details['enabled']) {
                continue;
            }

            // this image size is enabled, and
            // we have an uploaded image that we can use
            $uploaded_image_details[$size] = $this->UploadAndResize(null, $request->get("image_title"), $image_size_details, $photo);
        }


        // store the image upload.
        HessamUploadedPhoto::create([
            'image_title' => $request->get("image_title"),
            'source' => "ImageUpload",
            'uploader_id' => optional(\Auth::user())->id,
            'uploaded_images' => $uploaded_image_details,
        ]);


        return $uploaded_image_details;

    }


}