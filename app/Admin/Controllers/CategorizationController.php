<?php

namespace App\Admin\Controllers;

use App\Models\Categorization;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Category;

class CategorizationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'categorizations';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Categorization());

        $grid->column('category_id', __('category_id'));
        //$grid->column('category', __('category'));
        $grid->column('category.brand', 'カテゴリ');
        $grid->column('major_classification', __('major_classification'));
        $grid->column('classification', __('classification'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Categorization::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category', __('category'));
        $show->field('major_classification', __('major_classification'));
        $show->field('classification', __('classification'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Categorization());

        //$form->text('category', __('category'));

        // カテゴリ選択プルダウン
        $form->select('category_id', 'カテゴリ')
            ->options(Category::pluck('brand', 'id'))
            ->required();

        $form->text('major_classification', __('major_classification'));
        $form->text('classification', __('classification'));

        return $form;
    }
}
