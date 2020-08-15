<?php

namespace Flyingcoder\Sluggatong;

class Slug {

    protected $model;
    protected $column;

    public function __construct($model, $column)
    {
        $this->column = $column;

        $nameSpace = '\\App\\';

        $entity = app($nameSpace . ucfirst($model));

        $this->model = $entity;
    }

    /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function createSlug($title, $id = 0)
    {
        // Normalize the title
        $slug = $title;

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains($this->column, $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains($this->column, $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {

        return $this->model->select($this->column)->where($this->column, 'like', $slug.'%')
                           ->where('id', '<>', $id)
                           ->get();
    }
}