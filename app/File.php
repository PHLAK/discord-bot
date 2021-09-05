<?php

namespace App;

use Illuminate\Http\UploadedFile;

class File
{
    /** Create a new File. */
    private function __construct(private string $name, private string $content)
    {
    }

    /** Serialize the object. */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'content' => base64_encode($this->content),
        ];
    }

    /** Unserialize the object. */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->content = base64_decode($data['content']);
    }

    /** Create a new File from an UploadedFile. */
    public static function createFromUploadedFile(UploadedFile $file): self
    {
        return new self($file->getClientOriginalName(), $file->getContent());
    }

    /** Get the file name. */
    public function name(): string
    {
        return $this->name;
    }

    /** Get the file content. */
    public function content(): string
    {
        return $this->content;
    }
}
