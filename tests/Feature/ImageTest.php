<?php

namespace Tests\Feature;

use App\Blog;
use App\Image;
use App\Jobs\DownloadExternalImages;
use App\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public $remoteImageUrl = 'http://litlife.club/img/noimage.png';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateBmp()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $tmp = tmpfile();

        $imagick = new Imagick();
        $imagick->setFormat("bmp");
        $imagick->newImage(100, 100, new ImagickPixel('red'));
        $imagick->writeImageFile($tmp);

        $image = new Image();
        $image->openImage($tmp);
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $image->refresh();

        $this->assertNotNull($image);
        $this->assertEquals('jpeg', $image->type);
    }

    public function testCreateSvg()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $tmp = tmpfile();

        $imagick = new Imagick();
        $imagick->setFormat("svg");
        $imagick->newImage(100, 100, new ImagickPixel('red'));
        $imagick->writeImageFile($tmp);

        try {
            $image = new Image();
            $image->openImage($tmp);
            $image->storage = config('filesystems.default');
            $user->images()->save($image);
        } catch (Exception $exception) {

        }

        $this->assertNull($user->images()->first());
    }


    public function testCreatePng()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $tmp = tmpfile();

        $imagick = new Imagick();
        $imagick->setFormat("png");
        $imagick->newImage(100, 100, new ImagickPixel('red'));
        $imagick->writeImageFile($tmp);

        $image = new Image();
        $image->openImage($tmp);
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $this->assertNotNull($user->images()->first());
    }

    public function testDownloadExternalNotImage()
    {
        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        $url = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js';

        $blog->bb_text = '[img]'.$url.'[/img]';
        $blog->save();
        $blog->refresh();

        $this->assertStringContainsString($url, $blog->bb_text);
        $this->assertNull($user->images()->first());
    }

    public function testDownloadExternalImages()
    {
        Storage::fake(config('filesystems.default'));

        config(['litlife.site_hosts' => []]);

        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        $url = $this->remoteImageUrl;

        $blog->bb_text = '[img]'.$url.'[/img]';
        $blog->save();
        $blog->refresh();

        $this->assertStringNotContainsString($url, $blog->bb_text);
        $this->assertNotNull($user->images()->first());
    }

    public function testDownloadWrongImage()
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        //$url = Faker::create()->imageUrl(rand(100, 500), rand(600, 500));

        $blog->bb_text = '[img]http://#u<a>5a7cbca264s#[/img] test';
        $blog->save();
        $blog->refresh();

        //$this->assertNotContains($url, $blog->bb_text);
        $this->assertNull($user->images()->first());
    }

    /*
        public function testBigFile()
        {
            Storage::fake(config('filesystems.default'));

            $user = User::factory()->create();

            $blog = Blog::factory()->create(['create_user_id' => $user->id])
                ->fresh();

            $url = 'http://2.bp.blogspot.com/-W1w_Calw450/UyFBDM1idpI/AAAAAAADZTY/1SL6qWV_m44/s1600/the_tree_of_life__gif__by_luisbc-d6uiimk.gif';

            $blog->bb_text = '[img]'.$url.'[/img]';
            $blog->save();
            $blog->refresh();

            $this->assertNotContains($url, $blog->bb_text);
            $this->assertNotNull($user->images()->first());
        }
    */
    public function testValidBase64Image()
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        $image_path = $this->fakeImageStream();

        $image_base64 = base64_encode(file_get_contents($image_path));

        $blog->bb_text = '[img]data:image/jpg;base64,'.$image_base64.'[/img]';
        $blog->save();
        $blog->refresh();

        //$this->assertEquals('text', $blog->bb_text);
        $this->assertStringNotContainsString($image_base64, $blog->bb_text);
        $this->assertNotNull($user->images()->first());
    }

    public function testInvalidBase64ImageContents()
    {
        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        $image_base64 = base64_encode('invalid image contents');

        $blog->bb_text = 'text [img]data:image/jpg;base64,'.$image_base64.'[/img]';
        $blog->save();
        $blog->refresh();

        $this->assertEquals('text', $blog->bb_text);
        $this->assertStringNotContainsString($image_base64, $blog->bb_text);
        $this->assertNull($user->images()->first());
    }

    public function testDownloadInvalidExternalImages()
    {
        $user = User::factory()->create();

        $blog = Blog::factory()->create(['create_user_id' => $user->id])
            ->fresh();

        $url = 'http://example.com/'.uniqid().'.jpg';

        $blog->bb_text = '[img]'.$url.'[/img]';
        $blog->save();
        $blog->refresh();

        $this->assertStringContainsString($url, $blog->bb_text);
        $this->assertNull($user->images()->first());


        $url = 'https://'.uniqid().'.'.uniqid().'/'.uniqid().'.jpg';

        $blog->bb_text = '[img]'.$url.'[/img]';
        $blog->save();
        $blog->refresh();

        $this->assertStringContainsString($url, $blog->bb_text);
        $this->assertNull($user->images()->first());
    }

    public function testImagickSignature()
    {
        $imagick = new Imagick(__DIR__.'/images/test.jpeg');

        $this->assertEquals('e02e2762a125b0aa3fb4ee23fce4e95188e3f47ed499bb4eaa29071e7b9d496e',
            $imagick->getImageSignature());

        $this->assertEquals('99346', $imagick->getImageLength());

        $tmp = tmpfile();

        $imagick->writeImageFile($tmp);

        $imagick = new Imagick();
        $imagick->readImageFile($tmp);

        $this->assertEquals('72c358ff8a73044e3c08db62e77d61ae9e9a7d217b9dcd2bf39af79051cfcd6c',
            $imagick->getImageSignature());

        $this->assertTrue(in_array($imagick->getImageLength(), ['99111', '101140']));

        $tmp = tmpfile();

        $imagick->writeImageFile($tmp);

        $imagick = new Imagick();
        $imagick->readImageFile($tmp);

        $this->assertEquals('c0a04b088a4f4a8320e32a81ac5817925b2537501f2631e92f5ab1db1f52d419',
            $imagick->getImageSignature());

        $this->assertTrue(in_array($imagick->getImageLength(), ['98949', '100921']));

        $imagick = new Imagick();
        $imagick->readImageFile($tmp);

        $this->assertEquals('c0a04b088a4f4a8320e32a81ac5817925b2537501f2631e92f5ab1db1f52d419',
            $imagick->getImageSignature());

        $this->assertTrue(in_array($imagick->getImageLength(), ['100921', '98949']));
    }

    public function testCreate()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $image = new Image();
        $image->openImage(__DIR__.'/images/test.jpeg');
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $this->assertTrue(in_array($image->size, ['99111', '101140']));
        $this->assertEquals('jpeg', $image->type);
        $this->assertEquals('test.jpeg', $image->name);
        $this->assertEquals('72c358ff8a73044e3c08db62e77d61ae9e9a7d217b9dcd2bf39af79051cfcd6c', $image->sha256_hash);
        $this->assertEquals('f1e8c8d6eba9d8e8', $image->phash);
    }

    public function testCreateWrongExtension()
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create()
            ->fresh();

        $image = new Image();
        $image->name = 'file.png';
        $image->openImage(__DIR__.'/images/test.jpeg');
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $this->assertEquals('file.jpeg', $image->name);
    }

    public function testCreateAndCheckSame()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $image_path = $this->fakeImageStream();

        $image = new Image();
        $image->openImage($image_path);
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $image = new Image;
        $image->openImage($image_path);

        $this->assertNotNull(Image::sha256Hash($image->getImagick()->getImageSignature())->first());
    }

    public function testCreateBigSizeJpeg()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $file = tmpfile();

        $imagick = new Imagick();
        $imagick->newImage(2200, 500, new ImagickPixel('red'));
        $imagick->setImageFormat("jpeg");
        //$this->imagick = new \Imagick($file);
        $imagick->writeImageFile($file);

        $image = new Image();
        $image->openImage($file);
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $image = new Image;
        $image->openImage($file);

        $this->assertNotNull(Image::sha256Hash($image->getImagick()->getImageSignature())->first());

        $this->assertEquals(config('litlife.max_image_width'), $user->images()->first()->getImagick()->getImageWidth());
    }

    public function testCreateBigSizeGif()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        // create gif image
        $file = tmpfile();

        $animation = new Imagick();
        $animation->setFormat("gif");

        $colors = ['red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'violet'];

        foreach ($colors as $color) {
            $frame = new Imagick();
            $frame->setFormat("gif");
            $frame->newImage(2500, 10, new ImagickPixel($color));

            $draw = new ImagickDraw();
            $draw->setFontSize(30);
            $frame->annotateImage($draw, 0, 0, 0, $this->faker->realText(40));

            $animation->addImage($frame);
            $animation->setImageDelay(50);
            $animation->nextImage();

            $animation->writeImagesFile($file);
        }

        // check frames count
        $animation = new Imagick();
        $animation->readImageFile($file);
        $this->assertCount(count($colors), $animation->coalesceImages());

        $image = new Image();
        $image->openImage($file);
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $image = new Image;
        $image->openImage($file);

        $this->assertNotNull(Image::sha256Hash($image->getImagick()->getImageSignature())->first());

        $this->assertCount(count($colors), $user->images()->first()->getImagick()->coalesceImages());

        foreach ($user->images()->first()->getImagick()->coalesceImages() as $frame) {
            $this->assertEquals(config('litlife.animation_max_image_width'), $frame->getImageWidth());
        }
    }

    public function testGifImageSize()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $imagick = new Imagick(__DIR__.'/images/test.gif');

        $this->assertEquals(5, $imagick->getImageWidth());
        $this->assertEquals(3, $imagick->getImageHeight());

        foreach ($imagick->coalesceImages() as $frame) {
            $this->assertEquals(58, $frame->getImageWidth());
            $this->assertEquals(30, $frame->getImageHeight());
        }

        $image = new Image();
        $image->openImage(__DIR__.'/images/test.gif');
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $user->refresh();

        $this->assertEquals(58, $user->images()->first()->getRealWidth());
        $this->assertEquals(30, $user->images()->first()->getRealHeight());
    }

    public function testDownloadRemote()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $url = $this->remoteImageUrl;

        $image = new Image();
        $image->openImage($url, 'url');
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $this->assertNotNull($user->images()->first());
    }

    public function testDownloadWrongRemote()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $url = 'https://www.cloudflare.com/img/videos/stream-posters/'.uniqid().'.jpg';

        try {
            $image = new Image();
            $image->openImage($url, 'url');
            $image->storage = config('filesystems.default');
            $user->images()->save($image);
        } catch (ClientException $exception) {
            $this->assertNull($user->images()->first());
        } catch (ConnectException $exception) {

        }
    }

    public function testDownloadWrongDomain()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $url = 'http://'.uniqid().'.'.uniqid().'/'.uniqid().'.jpg';

        try {
            $image = new Image();
            $image->openImage($url, 'url');
            $image->storage = config('filesystems.default');
            $user->images()->save($image);
        } catch (ClientException $exception) {

        } catch (ConnectException $exception) {
            $this->assertNull($user->images()->first());
        }
    }

    public function testDownloadWrongImageName()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $url = 'http://example.com/';

        try {
            $image = new Image();
            $image->openImage($url, 'url');
            $image->storage = config('filesystems.default');
            $user->images()->save($image);
        } catch (ImagickException $exception) {
            $this->assertNull($user->images()->first());
        }
    }

    public function testCreateFromBase64Image()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $image_path = $this->fakeImageStream();

        $image = new Image();
        $image->openImage('data:image/jpeg;base64,'.base64_encode(file_get_contents($image_path)));
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $this->assertNotNull($user->images()->first());
    }

    public function testCreateFromNotImage()
    {
        $user = User::factory()->create()
            ->fresh();

        Storage::fake(config('filesystems.default'));

        $file = __DIR__.'/Books/test.epub';

        try {
            $image = new Image();
            $image->openImage($file);
            $image->storage = config('filesystems.default');
            $user->images()->save($image);

        } catch (ImagickException $exception) {
            $this->assertNull($user->images()->first());
        }
    }

    public function testUploadHttp()
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create()
            ->fresh();

        $upload = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->post(route('images.store'), [
                'upload' => $upload
            ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            )
            ->assertSessionHasNoErrors()
            ->assertJsonMissingValidationErrors()
            ->assertStatus(201);

        $image = $user->fresh()->images()->first();

        $this->assertNotNull($image);

        $response->assertJsonFragment([$image->url]);
        $this->assertTrue($image->exists());

        Storage::disk($image->storage)
            ->delete($image->dirname.'/'.$image->name);

        $this->assertFalse($image->exists());

        $response = $this->actingAs($user)
            ->post(route('images.store'), [
                'upload' => $upload
            ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            )
            ->assertSessionHasNoErrors()
            ->assertJsonMissingValidationErrors()
            ->assertStatus(201);

        $new_image = $user->fresh()->images()->orderBy('id', 'desc')->first();

        $this->assertNotNull($new_image);

        $this->assertNotEquals($image->id, $new_image->id);
    }

    public function testOpenBlobNotThroughImagick()
    {
        $blob = file_get_contents(__DIR__.'/images/test.jpeg');

        $image = new Image();
        $image->openImageNotThroughImagick($blob, 'blob');

        $this->assertEquals($blob, stream_get_contents($image->getSource()));
    }

    public function testOpenNotThroughImagickThenGetSignature()
    {
        $blob = file_get_contents(__DIR__.'/images/test.jpeg');

        $imagick = new Imagick();
        $imagick->readImageBlob($blob);

        $image = new Image();
        $image->openImageNotThroughImagick($blob, 'blob');

        $this->assertEquals($imagick->getImageSignature(), $image->getSha256Hash());
    }

    public function testRenameImageIfAlreadyExistsWithSameNameInFolder()
    {
        $user = User::factory()->create();

        Storage::fake(config('filesystems.default'));

        $image_path = $this->fakeImageStream();

        $name = Str::random(200);

        $image = new Image();
        $image->openImage($image_path);
        $image->name = $name;
        $image->storage = config('filesystems.default');
        $user->images()->save($image);

        $image->refresh();

        $image_path = $this->fakeImageStream();

        $image2 = new Image();
        $image2->openImage($image_path);
        $image2->name = $name;
        $image2->storage = config('filesystems.default');
        $user->images()->save($image2);

        $this->assertEquals(2, $user->images()->count());

        $this->assertNotEquals($image->url, $image2->url);
    }

    /*
        public function testDebugbarDisable()
        {
            Storage::fake(config('filesystems.default'));

            putenv('APP_ENV=production');

            $user = User::factory()->create()
                ->fresh();

            $upload = UploadedFile::fake()->image('avatar.jpg');

            $response = $this->actingAs($user)
                ->post(route('images.store'), [
                    'upload' => $upload
                ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']
                )
                ->assertSessionHasNoErrors()
                ->assertJsonMissingValidationErrors()
                ->assertStatus(201);
        }
        */
}
