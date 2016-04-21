<?php

namespace Infinety\Gallery\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Infinety\Gallery\Models\GalleryCategories;
use starter\Http\Controllers\Controller;
use Infinety\Gallery\Models\Categories;
use Infinety\Gallery\Models\Gallery;
use Infinety\Gallery\Models\Photos;
use starter\Http\Locale;
use Yajra\Datatables\Datatables;
use Infinety\Gallery\Facades\PhotoUploadFacade;

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
    public function getIndex()
    {
        return $this->firstViewThatExists('admin/gallery/list', 'gallery::admin.list');
    }

    /**
     * Get data of Galleries.
     *
     * @return mixed
     */
    public function getData()
    {
        $galleries = Gallery::select('id', 'title');

        return Datatables::of($galleries)

            ->addColumn('image', function ($gallery) {
              return '<img class="image_thumb" src='.$gallery->getPrincipalPhoto().'>';
            })
            ->addColumn('action', function ($gallery) {
                $actions = '<a href="galleries/gallery/'.$gallery->id.'" class="btn btn-xs btn-success m-r-10"><i class="glyphicon glyphicon-eye-open"></i> '.trans('kgallery.options.see').'</a>';
                $actions .= '<a href="galleries/edit/'.$gallery->id.'" class="btn btn-xs btn-primary m-r-10"><i class="glyphicon glyphicon-edit"></i> '.trans('kgallery.options.edit').'</a>';
                $actions .= '<a href="galleries/gallery/'.$gallery->id.'" class="btn btn-xs btn-danger trash"><i class="glyphicon glyphicon-trash"></i> '.trans('kgallery.options.delete').'</a>';

                return $actions;
            })
            ->addColumn('categories', function ($gallery) {
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
    public function getCreate()
    {
        $categories = GalleryCategories::all();
        return $this->firstViewThatExists('admin/gallery/list', 'gallery::admin.list', ['action' => 'create', 'categories' => $categories]);
    }

    /**
     * Create form post.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:gallery|max:255',
        ]);
        $gallery = Gallery::create($request->all());
        if ($gallery) {
            if ($request->has('cat') && is_array($request['cat'])) {
                $gallery->categories()->sync($request['cat']);
            }
        }
        Session::flash('message', trans('kgallery.messages.success'));

        return redirect()->to('admin/galleries/gallery/'.$gallery->id);
    }

    /**
     * Edit gallery.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getEdit($id)
    {
        $gallery = Gallery::find($id);
        if ($gallery) {
            $categories = GalleryCategories::all();
            return $this->firstViewThatExists('admin/gallery/form', 'gallery::admin.form', ['action' => 'edit', 'gallery' => $gallery, 'categories' => $categories, 'galleryCategories' => $gallery->categories()->lists('id')]);
        }

        return redirect()->to('admin/gallery');
    }

    /**
     * Update gallery name.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function putIndex(Request $request)
    {
        $this->validate($request, [
            'gallery_id'    => 'required',
        ]);

        $gallery = Gallery::find($request->get('gallery_id'));

        //Check title is unique
        $existsTitle = Gallery::whereTitle($request->get('title'))->count();

        if ($gallery) {
            if ($existsTitle == 0) {
                $gallery->title = $request->get('title');
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

        return redirect()->to('admin/galleries');
    }

    /**
     * Show Gallery Photos view.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getGallery($id)
    {
        $gallery = Gallery::find($id);
        if ($gallery) {
            $locales = Locale::where('state', 1)->get();
            return $this->firstViewThatExists('admin/gallery/photos', 'gallery::admin.photos', compact('gallery', 'locales'));
        }

        return redirect()->to('admin/galleries');
    }

    /**
     * Handles the upload file, saves to db and return data to the response.
     *
     * @param Request $request
     * @return bool|string
     */
    public function postUpload(Request $request)
    {
        $gallery = Gallery::find($request['gallery_id']);
        $lastPosition = Photos::filterByGallery($gallery->id)->max('position');
        if ($gallery) {
            $file = PhotoUploadFacade::photoUpload($request['file'], $gallery->slug);
            if ($file) {
                $data = [
                    'file'          =>  $file,
                    'position'      =>  ($lastPosition != 0) ? $lastPosition + 1 : 0,
                    'state'         =>  1,
                    'gallery_id'    =>  $gallery->id,
                ];
                $photo = Photos::create($data);

                return json_encode(
                            [
                                    'id'            =>  $photo->id,
                                    'file'          =>  $photo->getUrl(),
                                    'delete_url'    =>  url('admin/galleries/photo/'.$photo->id),
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
     * @return mixed
     */
    public function putPhotoinfo(Request $request)
    {

        $photo = Photos::findOrFail($request['pk']);
        $photo->translate($request['lang'])->$request['name'] = $request->get('value');
        $photo->save();

        return json_encode(true);
    }

    /**
     * Reorder Images position.
     *
     * @param Request $request
     * @return string
     */
    public function postReorder(Request $request)
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
     * @return string
     */
    public function deletePhoto($id)
    {
        $photo = Photos::findOrFail($id);
        if ($photo->delete()) {
            return json_encode(true);
        } else {
            abort('404', 'Error on delete');
        }
    }

    public function deleteGallery($id)
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
