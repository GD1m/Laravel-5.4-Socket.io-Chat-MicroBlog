<?php

namespace App;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentImage extends AttachmentAbstract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'image',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * @var Message
     */
    protected $message;

    /**
     * Mutator for image attribute
     *
     * @return mixed
     */
    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string|false
     */
    public static function storeImageToTempDirectory(UploadedFile $uploadedFile)
    {
        $newFileName = $uploadedFile->store('/attachments/images', 'temp');

        return $newFileName;
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public static function moveStoredImageFromTempToPublic(string $fileName): bool
    {
        $file = Storage::disk('temp')->get($fileName);

        Storage::disk('public')->put($fileName, $file);

        return (bool)Storage::disk('temp')->delete($fileName);
    }

    /**
     * @param array $attributes
     */
    public function create(array $attributes = [])
    {
        $image = $attributes['value'];

        self::moveStoredImageFromTempToPublic($image);

        $this->fill([
            'image' => $image,
        ]);

        $this->save();
    }
}
