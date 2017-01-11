<?php

namespace Infinety\Gallery\Controllers;

use App\Http\Controllers\Controller;
use App\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Infinety\Gallery\Models\GalleryCategories;
use Infinety\Gallery\Models\GalleryCategoriesTranslations;
use Yajra\Datatables\Datatables;

class CategoryController extends Controller
{
    /**
     * CategoryController constructor.
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
        $locales = Locale::getAvailables();

        return $this->firstViewThatExists('vendor/infinety/gallery/categories', 'gallery::admin.categories', compact('locales'));
    }

    /**
     * Get Data of Categories.
     *
     * @return mixed
     */
    public function getData()
    {
        $categories = GalleryCategories::all();

        return Datatables::of($categories)
            ->addColumn('title', function ($category) {
                return $category->title;
            })
            ->addColumn('action', function ($category) {
                $actions = '<a href="" data-edit="'.$category->id.'" data-toggle="modal" data-target="#editModal" class="btn btn-xs btn-complete mr5"><i class="fa fa-pencil"></i> '.trans('kgallery.options.edit').'</a>';
                $actions .= '<a href="k_categories/delete/'.$category->id.'" class="btn btn-xs btn-danger m-l-5" data-button-type="delete"><i class="fa fa-trash-o"></i> '.trans('kgallery.options.delete').'</a>';

                return $actions;
            })
            ->make(true);
    }

    /**
     * Create new category.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = array();
        $locales = Locale::getAvailables();
        foreach ($locales as $local) {
            $rules[] = ['title-'.$local->iso => 'required|unique:gallery_categories_translations|max:255'];
        }
        $this->validate($request, $rules);

        $category = new GalleryCategories();
        $translatedAttributes = [];
        foreach ($locales as $local) {
            $data = [
                'locale' => $local->iso,
                'title' => $request['title-'.$local->iso],
            ];
            array_push($translatedAttributes, $data);
        }
        $category->save();
        $category->translations()->createMany($translatedAttributes);

        Session::flash('message', trans('kgallery.messages.success'));

        return redirect()->back();
    }

    /**
     * Galleries list.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(GalleryCategories $category)
    {
        return response()->json($category);
    }

    /**
     * Updates given category.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GalleryCategories $category, Request $request)
    {
        $locales = Locale::getAvailables();
        $titles = [];
        foreach ($locales as $local) {
            $titles[] = $request->get('title-'.$local->iso);
        }

        $existsTitle = GalleryCategoriesTranslations::whereTitle($titles)->count();

        if ($category) {
            if ($existsTitle == 0) {
                foreach ($locales as $local) {
                    $translated = $category->translate($local->iso);
                    $translated->title = $request->get('title-'.$local->iso);
                    $translated->save();
                }
                $category->save();
                Session::flash('message', trans('kgallery.messages.success'));

                return redirect()->back();
            }
        }
        Session::flash('error', trans('kgallery.messages.error'));

        return redirect()->back();
    }

    /**
     * Delete given Category.
     *
     * @param $id
     *
     * @return string
     */
    public function deleteDelete($id)
    {
        $category = GalleryCategories::findOrFail($id);
        if ($category->delete()) {
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
