<?php

namespace Infinety\Gallery\Controllers;

use App\Http\Controllers\Controller;
use App\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Infinety\Gallery\Facades\PhotoUploadFacade;
use Infinety\Gallery\Models\Categories;
use Infinety\Gallery\Models\Gallery;
use Infinety\Gallery\Models\GalleryCategories;
use Infinety\Gallery\Models\Photos;
use Yajra\Datatables\Datatables;

class GalleryController extends Controller
{
    /**
     * GalleryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Galleries list.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return $this->firstViewThatExists('vendor/infinety/gallery/index', 'gallery::admin.index');
    }

    /**
     * Get data of Galleries.
     *
     * @return mixed
     */
    public function getData()
    {
        // $galleries = Gallery::with(['translations'])->select('gallery.*');

        $galleries = DB::table('gallery')
            ->join('gallery_translations', 'gallery_translations.gallery_id', '=', 'gallery.id')
            ->join('gallery_gallery_categories', 'gallery_gallery_categories.gallery_id', '=', 'gallery.id')
            ->join('gallery_categories', 'gallery_categories.id', '=', 'gallery_gallery_categories.gallery_categories_id')
            ->leftJoin('gallery_categories_translations', 'gallery_categories_translations.gallery_categories_id', '=', 'gallery_categories.id')
            ->select(['gallery.id', 'gallery_translations.title'])
            ->groupBy(['gallery.id']);

        return Datatables::of($galleries)
            ->addColumn('title', function ($gallery) {
                $gallery = Gallery::find($gallery->id);

                return $gallery->title;
            })
            ->addColumn('image', function ($gallery) {
                $gallery = Gallery::find($gallery->id);

                return '<img class="image_thumb" src='.$gallery->getPrincipalPhoto().'>';
            })
            ->addColumn('action', function ($gallery) {
                $gallery = Gallery::find($gallery->id);
                $actions = '<a href="galleries/'.$gallery->id.'" class="btn btn-xs btn-success mr10"><i class="glyphicon glyphicon-eye-open"></i> '.trans('kgallery.options.see').'</a>';
                $actions .= '<a href="galleries/'.$gallery->id.'/edit" class="btn btn-xs btn-primary mr10"><i class="glyphicon glyphicon-edit"></i> '.trans('kgallery.options.edit').'</a>';
                $actions .= '<a href="galleries/'.$gallery->id.'" class="btn btn-xs btn-danger" data-button-type="delete"><i class="glyphicon glyphicon-trash"></i> '.trans('kgallery.options.delete').'</a>';

                return $actions;
            })
            ->addColumn('categoryList', function ($gallery) {
                $gallery = Gallery::find($gallery->id);
                $collection = $gallery->categories;

                return $collection->implode('title', ', ');
            })
            ->make(true);
    }

    /**
     * Form to Create new Gallery.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = GalleryCategories::all();
        $locales = Locale::getAvailables();

        return $this->firstViewThatExists('vendor/infinety/gallery/form', 'gallery::admin.form', ['action' => 'create', 'categories' => $categories, 'locales' => $locales]);
    }

    /**
     * Create form post.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = array();
        $locales = Locale::where('state', 1)->get();
        foreach ($locales as $local) {
            $rules[] = ['title-'.$local->iso => 'required|unique:gallery_translations|max:255'];
        }
        $this->validate($request, $rules);

        $gallery = new Gallery();
        $translatedAttributes = [];
        foreach ($locales as $local) {
            $data = [
                'locale' => $local->iso,
                'title' => $request['title-'.$local->iso],
            ];
            array_push($translatedAttributes, $data);
        }

        if ($gallery->save()) {
            $gallery->translations()->createMany($translatedAttributes);
            if ($request->has('cat') && is_array($request['cat'])) {
                $gallery->categories()->sync($request['cat']);
            }
        }
        Session::flash('message', trans('kgallery.messages.success'));

        return redirect()->route('galleries.show', $gallery->id);
    }

    /**
     * Edit gallery.
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $gallery = Gallery::find($id);
        if ($gallery) {
            $categories = GalleryCategories::all();
            $locales = Locale::getAvailables();

            return $this->firstViewThatExists('vendor/infinety/gallery/form', 'gallery::admin.form', ['action' => 'edit', 'gallery' => $gallery, 'locales' => $locales, 'categories' => $categories, 'galleryCategories' => $gallery->categories()->pluck('id')]);
        }

        return redirect()->route('galleries.index');
    }

    /**
     * Update gallery name.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'gallery_id' => 'required',
        ]);

        $gallery = Gallery::find($request->get('gallery_id'));

        $locales = Locale::where('state', 1)->get();

        if ($gallery) {
            foreach ($locales as $local) {
                $gallery->translate($local->iso)->title = $request['title-'.$local->iso];
            }

            if ($request->has('cat') && is_array($request['cat'])) {
                $gallery->categories()->sync($request['cat']);
            }

            if ($gallery->save()) {
                Session::flash('message', trans('kgallery.messages.success'));
            } else {
                Session::flash('error', trans('kgallery.messages.error'));
            }
        } else {
            Session::flash('error', trans('kgallery.messages.error'));
        }

        return redirect()->route('galleries.index');
    }

    /**
     * Show Gallery Photos view.
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $gallery = Gallery::find($id);
        if ($gallery) {
            $locales = Locale::where('state', 1)->get();

            return $this->firstViewThatExists('vendor/infinety/gallery/photos', 'gallery::admin.photos', compact('gallery', 'locales'));
        }

        return redirect()->route('galleries.index');
    }

    /**
     * Handles the upload file, saves to db and return data to the response.
     *
     * @param Request $request
     *
     * @return bool|string
     */
    public function uploadPhoto(Request $request)
    {
        $gallery = Gallery::find($request['gallery_id']);
        $lastPosition = Photos::filterByGallery($gallery->id)->max('position');
        if ($gallery) {
            $file = PhotoUploadFacade::photoUpload($request['file'], $gallery->id);
            if ($file) {
                $data = [
                    'file' => $file,
                    'position' => ($lastPosition != 0) ? $lastPosition + 1 : 0,
                    'state' => 1,
                    'gallery_id' => $gallery->id,
                ];
                $photo = Photos::create($data);

                //Creamos las traduciones vacias
                $translatedAttributes = [];
                $locales = Locale::getAvailables();
                foreach ($locales as $local) {
                    $data = [
                        'locale' => $local->iso,
                        'name' => null,
                        'description' => null,
                        'photo_id',
                    ];
                    array_push($translatedAttributes, $data);
                }
                $photo->translations()->createMany($translatedAttributes);

                return json_encode(
                    [
                        'id' => $photo->id,
                        'file' => $photo->getUrl(),
                        'delete_url' => route('delete-photo'),
                    ]
                );
            }
        }

        return false;
    }

    /**
     * Update the specified resource in photos.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function infoPhoto(Request $request)
    {
        $photo = Photos::findOrFail($request['pk']);

        $photo->translate($request['lang'])->{$request['name']} = $request->get('value');
        $photo->save();

        return json_encode(true);
    }

    /**
     * Reorder Images position.
     *
     * @param Request $request
     *
     * @return string
     */
    public function reorder(Request $request)
    {
        $data = $request->all();
        $collection = collect($data['ids']);
        $collection->each(function ($item, $key) {
            $photo = Photos::find($item['photo']);
            $photo->position = $item['position'];
            $photo->save();
        });

        return json_encode(true);
    }

    /**
     * Remove photo.
     *
     * @param $id
     *
     * @return string
     */
    public function deletePhoto(Request $request)
    {
        $photo = Photos::find($request->get('photo-id'));
        if ($photo->delete()) {
            return json_encode(true);
        } else {
            abort('404', 'Error on delete');
        }
    }

    /**
     * Remove gallery
     * Also removes all its photos.
     *
     * @param int $id
     *
     * @return json
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery->delete()) {
            return json_encode(true);
        } else {
            abort('404', 'Error on delete');
        }
    }

    /**
     * Allow replace the default views by placing a view with the same name.
     * If no such view exists, load the one from the package.
     *
     * @param $first_view
     * @param $second_view
     * @param array $information
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function firstViewThatExists($first_view, $second_view, $information = [])
    {
        // load the first view if it exists, otherwise load the second one
        if (view()->exists($first_view)) {
            return view($first_view, $information);
        } else {
            return view($second_view, $information);
        }
    }
}
