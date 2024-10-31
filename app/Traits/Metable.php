<?php
namespace App\Traits;


use App\Models\Meta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;



/**
 * Trait for giving Eloquent models the ability to handle Meta.
 *
 * @property Collection|Meta[] $meta
 * @method static Builder whereHasMeta($key)
 * @method static Builder whereDoesntHaveMeta($key)
 * @method static Builder whereHasMetaKeys(array $keys)
 * @method static Builder whereMeta($key, $operator, $value = null)
 * @method static Builder whereMetaIn(string $key, array $values)
 */

trait Metable
{


    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metable');
    }
    public function getAllMeta()
    {

        return collect($this->meta()->pluck('value', 'key'));
    }
    public function hasMeta($key)
    {
        $meta = $this->meta()->where('key', $key)->get();

        return (bool) count($meta);
    }
    public function getMeta($key, $default = null)
    {
        if ($meta = $this->meta()->where('key', $key)->first()) {
            return $meta;
        }

        return $default;
    }
    public function getMetaValue($key)
    {
        return $this->hasMeta($key) ? $this->getMeta($key)->value : null;
    }
    public function addMeta($key, $value)
    {
        if (! $this->meta()->where('key', $key)->count()) {
            return $this->meta()->create([
                'key'   => $key,
                'value' => $value,
            ]);
        }
    }
    public function updateMeta($key, $value)
    {
        if ($meta = $this->getMeta($key)) {
            $meta->value = $value;

            return $meta->save();
        }
        return false;
    }
    public function setMeta($key, $value)
    {
        return $this->hasMeta($key) ? $this->updateMeta($key, $value) : $this->addMeta($key, $value);
    }
    public function setManyMeta(array $metaDictionary)
    {
        if (empty($metaDictionary)) {
            return;
        }
        foreach ($metaDictionary as $key => $value) {
            $this->setMeta($key, $value);
        }
    }
    public function deleteMeta($key, $value = null)
    {
        return $value
            ? $this->meta()->where('key', $key)->where('value', $value)->delete()
            : $this->meta()->where('key', $key)->delete();
    }
    public function deleteManyMeta(array $arr)
    {
        if($this->isAssoc($arr)){
            foreach ($arr as $key => $value) {
                $this->deleteMeta($key, $value);
            }
        }
        else{
            $this->meta()->whereIn('key', $arr)->delete();
        }

    }
    public function deleteAllMeta()
    {
        return $this->meta()->delete();
    }
    function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    ///--------------------------------------Support Scope ----------------------------------///

    public function scopeWhereHasMeta(Builder $q, $key)
    {
        $q->whereHas('meta', function (Builder $q) use ($key) {
            $q->whereIn('key', (array)$key);
        });
    }

    public function scopeWhereDoesntHaveMeta(Builder $q, $key)
    {
        $q->whereDoesntHave('meta', function (Builder $q) use ($key) {
            $q->whereIn('key', (array)$key);
        });
    }
    public function scopeWhereHasMetaKeys(Builder $q, array $keys)
    {
        return $q->whereHas(
            'meta',
            function (Builder $q) use ($keys) {
                $q->whereIn('key', $keys);
            },
            '=',
            count($keys)
        );
    }
    public function scopeWhereMeta(Builder $q, $key, $operator, $value = null)
    {
        // Shift arguments if no operator is present.
        if (!isset($value)) {
            $value = $operator;
            $operator = '=';
        }


        return $q->whereHas('meta', function (Builder $q) use ($key, $operator, $value) {
            $q->where('key', $key);
            $q->where('value', $operator, $value);
        });
    }
    public  function scopeWhereMetaIn(Builder $q,string  $key, array $values)
    {
        return $q->whereHas('meta', function (Builder $q) use ($key, $values) {
            $q->where('key', $key);
            $q->whereIn('value', $values);
        });
    }

    // private function joinMetaTable(Builder $q, string $key, $type = 'left'): string
    //    {
    //        $relation = $this->meta();
    //        $metaTable = $relation->getRelated()->getTable();
    //
    //        // Create an alias for the join, to allow the same
    //        // table to be joined multiple times for different keys.
    //        $alias = $metaTable . '__' . $key;
    //
    //        // If no explicit select columns are specified,
    //        // avoid column collision by excluding meta table from select.
    //        if (!$q->getQuery()->columns) {
    //            $q->select($this->getTable() . '.*');
    //        }
    //
    //        // Join the meta table to the query
    //        $q->join("{$metaTable} as {$alias}", function (JoinClause $q) use ($relation, $key, $alias) {
    //            $q->on($relation->getQualifiedParentKeyName(), '=', $alias . '.' . $relation->getForeignKeyName())
    //                ->where($alias . '.key', '=', $key)
    //                ->where($alias . '.' . $relation->getMorphType(), '=', $this->getMorphClass());
    //        }, null, null, $type);
    //
    //        // Return the alias so that the calling context can
    //        // reference the table.
    //        return $alias;
    //    }


    public static function bootMetable()
    {

        // delete all attached meta on deletion
        static::deleted(function (self $model) {
            $model->deleteAllMeta();

        });
    }


}
