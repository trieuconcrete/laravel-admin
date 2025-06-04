<?php

namespace Tests\Unit;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use App\Services\SettingService;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class DateFormatValidationTest extends TestCase
{
    use UsesSystemDateFormat;

    protected $settingService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the SettingService
        $this->settingService = $this->createMock(SettingService::class);
        App::instance(SettingService::class, $this->settingService);
    }
    
    public function testDateFormatValidationRuleWithDefaultFormat()
    {
        // Setup the mock to return the default format
        $this->settingService->method('get')
            ->with('date_format', 'd/m/Y')
            ->willReturn('d/m/Y');
            
        // Get the validation rule
        $rule = $this->getSystemDateFormatRule();
        
        // Assert that the rule contains the correct format
        $this->assertEquals('date|date_format:dd/mm/yyyy', $rule);
    }
    
    public function testDateFormatValidationRuleWithCustomFormat()
    {
        // Setup the mock to return a custom format
        $this->settingService->method('get')
            ->with('date_format', 'd/m/Y')
            ->willReturn('Y-m-d');
            
        // Get the validation rule
        $rule = $this->getSystemDateFormatRule();
        
        // Assert that the rule contains the correct format
        $this->assertEquals('date|date_format:yyyy-mm-dd', $rule);
    }
    
    public function testDateFormatValidationRuleWithAnotherCustomFormat()
    {
        // Setup the mock to return another custom format
        $this->settingService->method('get')
            ->with('date_format', 'd/m/Y')
            ->willReturn('m/d/Y');
            
        // Get the validation rule
        $rule = $this->getSystemDateFormatRule();
        
        // Assert that the rule contains the correct format
        $this->assertEquals('date|date_format:mm/dd/yyyy', $rule);
    }
}
