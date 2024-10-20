<?php

namespace App;

use Illuminate\Http\UploadedFile;

class File
{
    public function __construct(
        public readonly string $name,
        public readonly string $extension,
        public readonly string $content
    ) {}

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'extension' => $this->extension,
            'content' => base64_encode($this->content),
        ];
    }

    /** @param array{name: string, extension: ?string, content: string} $data */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->extension = $data['extension'] ?? '';
        $this->content = base64_decode($data['content']);
    }

    public static function createFromUploadedFile(UploadedFile $file): self
    {
        return new self($file->getClientOriginalName(), $file->extension(), $file->getContent());
    }
}
