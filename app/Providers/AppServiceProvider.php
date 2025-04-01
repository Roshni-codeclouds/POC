<?php

namespace App\Providers;
use App\Models\Note;
use App\Policies\NotePolicy;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // $this->registerPolicies();
    }

    protected $policies = [
        Note::class => NotePolicy::class,
       
    ];

 

    
}
