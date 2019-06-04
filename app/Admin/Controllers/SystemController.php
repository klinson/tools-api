<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2018/10/17
 * Time: 23:07
 */

namespace App\Admin\Controllers;

use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    protected $pageHeader = '';

    public function index() {

    }

    public function aboutUs()
    {
        $this->pageHeader = '微信小程序 - 关于我们';

        return Admin::content(function (Content $content) {
            $this->_setPageDefault($content);

            $form = new Form();
            $form->action('/admin/system/aboutUs');
            $form->method();
            $form->editor('content', '关于我们')->default(config('wxapp_about_us', ''));

            $content->body(new Box($this->pageHeader, $form));
        });
    }

    public function storeAboutUs(Request $request)
    {
        $config = ConfigModel::where('name', 'wxapp_about_us')->first();
        if (! empty($config)) {
            $config->value = $request->get('content', '');
            $config->save();
        }
        return redirect()->back();
    }
}