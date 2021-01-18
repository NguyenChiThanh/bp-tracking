<?php

namespace App\Models\Storage;

use App\Adapters\File\FileLocalAdapter;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent Model: 'File'.
 *
 * @property int $id
 * @property string $name
 * @property string $file
 * @property string $disk
 * @property string $url_path
 * @property string $disk_path
 * @property string $type
 * @property string $size
 * @property string $extension
 */
class File extends Model
{
    const DISK_LOCAL = 'public';

    const TABLE_NAME = 'files';

    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'name',
        'file',
        'creator_id',
        'disk',
        'url_path',
        'disk_path',
        'type',
        'size',
        'extension',
    ];

    public function getAdapter()
    {
        switch ($this->disk) {
            case self::DISK_LOCAL:
            default:
                $adapter = new FileLocalAdapter($this);
                break;
        }

        return $adapter;
    }
}
