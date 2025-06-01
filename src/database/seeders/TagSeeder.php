<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Laravel tags
            'laravel', 'eloquent', 'blade', 'artisan', 'migration', 'seeder', 'middleware',
            'api', 'rest-api', 'authentication', 'authorization', 'laravel-8', 'laravel-9', 'laravel-10',
            
            // PHP tags
            'php', 'php8', 'composer', 'oop', 'design-patterns', 'solid-principles', 'psr',
            
            // JavaScript tags
            'javascript', 'es6', 'nodejs', 'vuejs', 'reactjs', 'angular', 'typescript',
            'jquery', 'ajax', 'webpack', 'npm', 'yarn',
            
            // Frontend tags
            'html', 'css', 'sass', 'tailwindcss', 'bootstrap', 'responsive-design',
            'ui-ux', 'frontend', 'spa', 'pwa',
            
            // Backend tags
            'backend', 'api-development', 'microservices', 'serverless', 'graphql',
            'websocket', 'queue', 'caching', 'redis', 'memcached',
            
            // Database tags
            'mysql', 'postgresql', 'mongodb', 'sqlite', 'database-design', 'sql',
            'nosql', 'database-optimization', 'indexing',
            
            // DevOps tags
            'docker', 'kubernetes', 'ci-cd', 'jenkins', 'gitlab-ci', 'github-actions',
            'nginx', 'apache', 'linux', 'ubuntu', 'centos',
            
            // Cloud tags
            'aws', 'ec2', 's3', 'lambda', 'azure', 'google-cloud', 'digital-ocean',
            'cloud-computing', 'iaas', 'paas', 'saas',
            
            // Testing tags
            'testing', 'unit-testing', 'phpunit', 'tdd', 'integration-testing',
            'e2e-testing', 'jest', 'cypress',
            
            // Security tags
            'security', 'csrf', 'xss', 'sql-injection', 'encryption', 'ssl',
            'oauth', 'jwt', 'web-security',
            
            // AI/ML tags
            'artificial-intelligence', 'machine-learning', 'deep-learning', 'tensorflow',
            'pytorch', 'nlp', 'computer-vision', 'data-science', 'python',
            
            // General programming
            'programming', 'coding', 'algorithms', 'data-structures', 'performance',
            'optimization', 'debugging', 'best-practices', 'clean-code', 'refactoring',
            
            // Tools & Others
            'git', 'github', 'gitlab', 'vscode', 'phpstorm', 'postman', 'swagger',
            'agile', 'scrum', 'project-management'
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName)
            ]);
        }
    }
}