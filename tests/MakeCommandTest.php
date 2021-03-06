<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeCommandTest extends TestCase
{
    /** @test */
    public function component_is_created_by_make_command()
    {
        Artisan::call('make:livewire foo');

        $this->assertTrue(File::exists($this->livewireClassesPath('Foo.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo.blade.php')));
    }

    /** @test */
    public function component_is_created_by_livewire_make_command()
    {
        Artisan::call('livewire:make foo');

        $this->assertTrue(File::exists($this->livewireClassesPath('Foo.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo.blade.php')));
    }

    /** @test */
    public function component_is_created_by_touch_command()
    {
        Artisan::call('livewire:touch foo');

        $this->assertTrue(File::exists($this->livewireClassesPath('Foo.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo.blade.php')));
    }

    /** @test */
    public function nested_component_is_created_by_make_command()
    {
        Artisan::call('make:livewire foo.bar');

        $this->assertTrue(File::exists($this->livewireClassesPath('Foo/Bar.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo/bar.blade.php')));
    }

    /** @test */
    public function multiword_component_is_created_by_make_command()
    {
        Artisan::call('make:livewire foo-bar');

        $this->assertTrue(File::exists($this->livewireClassesPath('FooBar.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo-bar.blade.php')));
    }

    /** @test */
    public function pascal_case_component_is_automatically_converted_by_make_command()
    {
        Artisan::call('make:livewire FooBar.FooBar');

        $this->assertTrue(File::exists($this->livewireClassesPath('FooBar/FooBar.php')));
        $this->assertTrue(File::exists($this->livewireViewsPath('foo-bar/foo-bar.blade.php')));
    }

    /** @test */
    public function new_component_class_view_name_reference_matches_configured_view_path()
    {
        // We can't use Artisan::call here because we need to be able to set config vars.
        $this->app['config']->set('livewire.view_path', resource_path('views/not-livewire'));
        $this->app[Kernel::class]->call('make:livewire foo', []);

        $this->assertStringContainsString(
            "view('not-livewire.foo')",
            File::get($this->livewireClassesPath('Foo.php'))
        );
        $this->assertTrue(File::exists(resource_path('views/not-livewire/foo.blade.php')));
    }

    /** @test */
    public function a_component_is_not_created_with_a_reserved_class_name()
    {
        Artisan::call('make:livewire component');

        $this->assertFalse(File::exists($this->livewireClassesPath('Component.php')));
        $this->assertFalse(File::exists($this->livewireViewsPath('component.blade.php')));
    }
}
