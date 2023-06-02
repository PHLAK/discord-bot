<?php

namespace App;

use Illuminate\Http\UploadedFile;

class File
{
    /** Create a new File. */
    public function __construct(
        public readonly string $name,
        public readonly string $extension,
        public readonly string $content
    ) {}

    /** Serialize the object. */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'extension' => $this->extension,
            'content' => base64_encode($this->content),
        ];
    }

    /** Unserialize the object. */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->extension = $data['extension'] ?? '';
        $this->content = base64_decode($data['content']);
    }

    /** Create a new File from an UploadedFile. */
    public static function createFromUploadedFile(UploadedFile $file): self
    {
        return new self($file->getClientOriginalName(), $file->extension(), $file->getContent());
    }
}
