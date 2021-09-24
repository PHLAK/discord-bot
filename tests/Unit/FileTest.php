<?php

namespace Tests\Unit;

use App\File;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

/** @covers \App\File */
class FileTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiate_from_an_uploaded_file(): void
    {
        $file = File::createFromUploadedFile(
            new UploadedFile(__DIR__ . '/../_data/test-file.txt', 'test-file.txt')
        );

        $this->assertEquals('test-file.txt', $file->name());
        $this->assertEquals('txt', $file->extension());
        $this->assertEquals('Test file; please ignore', $file->content());
    }

    /** @test */
    public function it_can_be_serialized_and_unserialized(): void
    {
        $file = File::createFromUploadedFile(
            new UploadedFile(__DIR__ . '/../_data/test-file.txt', 'test-file.txt')
        );

        $unserializedFile = unserialize(serialize($file));

        $this->assertEquals('test-file.txt', $unserializedFile->name());
        $this->assertEquals('txt', $unserializedFile->extension());
        $this->assertEquals('Test file; please ignore', $unserializedFile->content());
    }
}
