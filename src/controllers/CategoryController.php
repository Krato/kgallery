<?php namespace Infinety\Gallery\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use starter\Http\Controllers\Controller;
use Infinety\Gallery\Models\GalleryCategories;
use Yajra\Datatables\Datatables;

class CategoryController extends Controller {

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Galleries list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {
        return $this->firstViewThatExists('admin/gallery/list', 'gallery::admin.categories');
    }

    /**
     * Get Data of Categories
     *
     * @return mixed
     */
    public function getData()
    {
        $categories = GalleryCategories::select('id', 'title');
        return Datatables::of($categories)
            ->addColumn('action', function ($category) {
                $actions = '<a href="" data-edit="'.$category->id.'" data-toggle="modal" data-target="#editModal" class="btn btn-xs btn-primary m-r-10"><i class="glyphicon glyphicon-edit"></i> '.trans('kgallery.options.edit').'</a>';
                $actions.= '<a href="k_categories/delete/'.$category->id.'" class="btn btn-xs btn-danger trash"><i class="glyphicon glyphicon-trash"></i> '.trans('kgallery.options.delete').'</a>';
                return $actions;
            })
            ->make(true);
    }


    /**
     * Create new category
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        GalleryCategories::create($request->all());
        return redirect()->back();
    }


    /**
     * Updates given category
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function putIndex(Request $request)
    {
        $category = GalleryCategories::findOrFail($request->get('cat_id'));
        $existsTitle = GalleryCategories::whereTitle($request->get('title'))->count();

        if($category) {
            if ($existsTitle == 0) {
                $category->title = $request->get('title');
                $category->save();
                Session::flash('message', trans('kgallery.messages.success'));
                return redirect()->back();
            }
        }
        Session::flash('error', trans('kgallery.messages.error'));
        return redirect()->back();
    }


    /**
     * Delete given Category
     *
     * @param $id
     * @return string
     */
    public function deleteDelete($id)
    {
        $category = GalleryCategories::findOrFail($id);
        if($category->delete()){
            return json_encode(true);
        } else {
            abort('404', 'Error on delete');
        }
    }


    /**
     *
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
        if (view()->exists($first_view))
        {

            return view($first_view, $information);
        }
        else
        {
            return view($second_view, $information);
        }
    }
}