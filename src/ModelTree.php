<?php

namespace Krendil\ModelTree;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use App\Nova\Resource;

class ModelTree extends Tool
{
    protected Resource|null $resource = null;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('model-tree', __DIR__.'/../dist/js/tool.js');
        Nova::style('model-tree', __DIR__.'/../dist/css/tool.css');
    }

    public function resource(string $resource): self
    {
        if (class_exists($resource)) {
            $this->resource = new $resource;
        }

        return $this;
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuItem::make($this->getMenuName())
            ->path($this->getMenuPath());
    }

    protected function getMenuName(): string
    {
        if ($this->resource) {
            return ucfirst($this->resource::uriKey()) . ' Tree';
        }

        return 'Model Tree';
    }

    protected function getMenuPath(): string
    {
        if ($this->resource) {
            return '/model-tree/' . $this->resource::uriKey();
        }

        return '/model-tree';
    }
}
