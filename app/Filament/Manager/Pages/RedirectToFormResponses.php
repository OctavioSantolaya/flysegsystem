<?php

namespace App\Filament\Manager\Pages;

use Filament\Pages\Page;

class RedirectToFormResponses extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $title = 'Redirección';
    
    protected static bool $shouldRegisterNavigation = false;
    
    protected static string $routePath = '/redirect-to-form-responses';
    
    protected static string $view = 'filament.manager.pages.redirect-to-form-responses';
    
    public function mount(): void
    {
        $this->redirect(route('filament.manager.resources.form-responses.index'));
    }
}
