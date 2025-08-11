<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Carbon;

class Audio
{
    static $table       = 'ms_audio';
    static $allias      = 'au';
    static $columns     = ['id','agama_id','kategori','src','title','artist','album','artwork_src','artwork_sizes','artwork_type','created_at','updated_at','deleted_at'];
    static $readable    = ['id','agama_id','kategori','src','title','artist','album','artwork_src','artwork_sizes','artwork_type','created_at'];
    static $fillable    = ['agama_id','kategori','src','title','artist','album','artwork_src','artwork_sizes','artwork_type'];
    static $order       = ['created_at','DESC'];

    static function getColumns(array $whitelist=[]) : array {
        if($whitelist) {
            $columns = array_filter($whitelist, fn($column) => in_array($column, self::$columns));
        } else {
            $columns = self::$readable;
        }
        $mapped = array_map(function ($column) {
            return self::$allias.".{$column} AS {$column}";
        },  $columns);
        return $mapped;
    }

    static function getColumnAllias(array $whitelist=[]) : array {
        if($whitelist) {
            $columns = array_filter($whitelist, fn($column) => in_array($column,  self::$columns));
        } else {
            $columns = self::$readable;
        }
        $mapped = array_map(function ($column) {
            return self::$allias.".{$column} AS ".self::$allias."__{$column}";
        },  $columns);
        return $mapped;
    }

    static function getTable() : string {
        return self::$table." AS ".self::$allias;
    }

    static function getOrder() : string {
        return self::$allias.".".self::$order[0]." ".self::$order[1];
    }

    static function create($assoc) {
        $now = Carbon::now(new \DateTimeZone(env('TIMEZONE','Asia/Jakarta')));

        $columns = array_filter($assoc, fn($key) => in_array($key, self::$fillable), ARRAY_FILTER_USE_KEY);
        if(empty($columns)) throw new \Exception('Data does not match the column.');
        $columns['id'] = Uuid::uuid4()->toString();
        $columns['created_at'] = $columns['updated_at'] = $now;

        DB::table(self::$table)->insert($columns);
        $data  = DB::table(self::$table)->find($columns['id']);
        return (array) $data;
    }

    static function bulkCreate($arr_of_assoc) {
        $now = Carbon::now(new \DateTimeZone(env('TIMEZONE','Asia/Jakarta')));

        $arr_of_columns = array_map(function ($assoc) use ($now) {
            $columns = array_filter($assoc, fn($key) => in_array($key, self::$fillable), ARRAY_FILTER_USE_KEY);
            if(empty($columns)) throw new \Exception('Data does not match the column.');
            $columns['id'] = Uuid::uuid1()->toString();
            $columns['created_at'] = $columns['updated_at'] = $now;
            return $columns;
        }, $arr_of_assoc);

        DB::table(self::$table)->insert($arr_of_columns);
        $ids = array_map(fn($row)=>$row['id'],$arr_of_columns);
        $data  = DB::table(self::$table)->whereIn('id',$ids)->get()->toArray();
        return $data;
    }

    static function update($id,$assoc) {
        $now = Carbon::now(new \DateTimeZone(env('TIMEZONE','Asia/Jakarta')));

        $columns = array_filter($assoc, fn($key) => in_array($key, self::$fillable), ARRAY_FILTER_USE_KEY);
        if(empty($columns)) throw new \Exception('Data does not match the column.');

        $columns['updated_at'] = $now;
        DB::table(self::$table)->where('id',$id)->whereNull('deleted_at')->update($columns);
        $data  = DB::table(self::$table)->where('id',$id)->whereNull('deleted_at')->first();
        return (array) $data;
    }

    static function destroy($id) {
        $now = Carbon::now(new \DateTimeZone(env('TIMEZONE','Asia/Jakarta')));

        DB::table(self::$table)->where('id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now]);
        $data  = DB::table(self::$table)->where('id',$id)->first();
        return (array) $data;
    }
}
