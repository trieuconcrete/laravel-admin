<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        
        // Get all categories and tags
        $categories = Category::all();
        $tags = Tag::all();
        
        // Get admin user or create one
        $admin = User::first() ?? User::create([
            'name' => 'Admin',
            'email' => 'admin@nguyentrieu.site',
            'password' => bcrypt('password'),
        ]);
        
        // Sample posts data
        $posts = [
            [
                'title' => 'Tối ưu hóa Laravel Application với Caching Strategies',
                'content' => $this->getLaravelCachingContent(),
                'category' => 'laravel',
                'tags' => ['laravel', 'caching', 'redis', 'performance', 'optimization'],
                'views' => rand(500, 2000),
            ],
            [
                'title' => 'Xây dựng RESTful API với Laravel và Best Practices',
                'content' => $this->getLaravelApiContent(),
                'category' => 'laravel',
                'tags' => ['laravel', 'api', 'rest-api', 'backend', 'best-practices'],
                'views' => rand(800, 2500),
            ],
            [
                'title' => 'AWS Lambda và Serverless Architecture cho PHP Developers',
                'content' => $this->getAwsLambdaContent(),
                'category' => 'aws',
                'tags' => ['aws', 'lambda', 'serverless', 'php', 'cloud-computing'],
                'views' => rand(1000, 3000),
            ],
            [
                'title' => 'Docker cho Laravel Developers: Từ Development đến Production',
                'content' => $this->getDockerContent(),
                'category' => 'devops',
                'tags' => ['docker', 'laravel', 'devops', 'containerization', 'deployment'],
                'views' => rand(1200, 3500),
            ],
            [
                'title' => 'Vue.js 3 Composition API: Hướng dẫn cho người mới bắt đầu',
                'content' => $this->getVueContent(),
                'category' => 'javascript',
                'tags' => ['vuejs', 'javascript', 'frontend', 'composition-api', 'spa'],
                'views' => rand(600, 2000),
            ],
            [
                'title' => 'Database Optimization: Indexing Strategies trong MySQL',
                'content' => $this->getDatabaseContent(),
                'category' => 'database',
                'tags' => ['mysql', 'database', 'optimization', 'indexing', 'performance'],
                'views' => rand(900, 2800),
            ],
            [
                'title' => 'Python cho Web Developers: Django vs Flask',
                'content' => $this->getPythonContent(),
                'category' => 'python',
                'tags' => ['python', 'django', 'flask', 'web-development', 'backend'],
                'views' => rand(700, 2200),
            ],
            [
                'title' => 'CI/CD Pipeline với GitHub Actions cho Laravel Projects',
                'content' => $this->getCiCdContent(),
                'category' => 'devops',
                'tags' => ['ci-cd', 'github-actions', 'laravel', 'devops', 'automation'],
                'views' => rand(550, 1800),
            ],
            [
                'title' => 'Machine Learning cơ bản với Python và TensorFlow',
                'content' => $this->getMachineLearningContent(),
                'category' => 'ai-machine-learning',
                'tags' => ['machine-learning', 'python', 'tensorflow', 'ai', 'deep-learning'],
                'views' => rand(1500, 4000),
            ],
            [
                'title' => 'Clean Code: Nguyên tắc viết code sạch trong PHP',
                'content' => $this->getCleanCodeContent(),
                'category' => 'php',
                'tags' => ['php', 'clean-code', 'best-practices', 'solid-principles', 'refactoring'],
                'views' => rand(1100, 3200),
            ],
        ];
        
        // Create posts
        foreach ($posts as $index => $postData) {
            $category = $categories->where('slug', $postData['category'])->first();
            
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $faker->paragraph(3),
                'content' => $postData['content'],
                'author_id' => $admin->id,
                'category_id' => $category->id,
                'status' => 'published',
                'views' => $postData['views'],
                'likes' => rand(20, 200),
                'meta_title' => $postData['title'],
                'meta_description' => $faker->paragraph(1),
                'published_at' => now()->subDays(rand(1, 60)),
            ]);
            
            // Attach tags
            $postTags = $tags->whereIn('slug', array_map(fn($tag) => Str::slug($tag), $postData['tags']));
            $post->tags()->attach($postTags->pluck('id'));
        }
        
        // Generate additional random posts
        for ($i = 0; $i < 20; $i++) {
            $post = Post::create([
                'title' => $faker->sentence(rand(6, 10)),
                'slug' => Str::slug($faker->sentence(rand(6, 10))),
                'excerpt' => $faker->paragraph(3),
                'content' => $this->generateRandomContent($faker),
                'author_id' => $admin->id,
                'category_id' => $categories->random()->id,
                'status' => $faker->randomElement(['published', 'published', 'published', 'draft']),
                'views' => rand(100, 1000),
                'likes' => rand(5, 100),
                'meta_title' => $faker->sentence(6),
                'meta_description' => $faker->paragraph(1),
                'published_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
            
            // Attach random tags (3-6 tags per post)
            $post->tags()->attach($tags->random(rand(3, 6))->pluck('id'));
        }
    }
    
    private function getLaravelCachingContent(): string
    {
        return <<<HTML
<h2>Giới thiệu về Caching trong Laravel</h2>
<p>Caching là một trong những kỹ thuật quan trọng nhất để tối ưu hóa hiệu suất của ứng dụng Laravel. Trong bài viết này, chúng ta sẽ tìm hiểu về các strategies caching hiệu quả.</p>

<h3>1. Cache Drivers trong Laravel</h3>
<p>Laravel hỗ trợ nhiều cache drivers khác nhau:</p>
<ul>
    <li><strong>File</strong>: Lưu cache trong file system</li>
    <li><strong>Database</strong>: Lưu cache trong database</li>
    <li><strong>Redis</strong>: In-memory data store (recommended)</li>
    <li><strong>Memcached</strong>: Distributed memory caching system</li>
</ul>

<h3>2. Basic Cache Operations</h3>
<pre><code class="language-php">// Store item in cache
Cache::put('key', 'value', \$seconds);

// Store item forever
Cache::forever('key', 'value');

// Retrieve item
\$value = Cache::get('key');

// Retrieve with default
\$value = Cache::get('key', 'default');

// Check existence
if (Cache::has('key')) {
    //
}

// Remove item
Cache::forget('key');</code></pre>

<h3>3. Cache Tags (Redis/Memcached only)</h3>
<pre><code class="language-php">// Store tagged cache
Cache::tags(['people', 'artists'])->put('John', \$john, \$seconds);

// Flush tagged cache
Cache::tags(['people', 'authors'])->flush();</code></pre>

<h3>4. Query Result Caching</h3>
<pre><code class="language-php">// Cache database queries
\$users = Cache::remember('users', \$seconds, function () {
    return DB::table('users')->get();
});

// Cache forever
\$users = Cache::rememberForever('users', function () {
    return DB::table('users')->get();
});</code></pre>

<h3>5. Route Caching</h3>
<pre><code class="language-bash"># Cache routes
php artisan route:cache

# Clear route cache
php artisan route:clear</code></pre>

<h3>6. View Caching</h3>
<pre><code class="language-bash"># Cache views
php artisan view:cache

# Clear view cache
php artisan view:clear</code></pre>

<h3>Best Practices</h3>
<ul>
    <li>Sử dụng cache tags để quản lý cache hiệu quả</li>
    <li>Set TTL (Time To Live) phù hợp cho mỗi loại data</li>
    <li>Implement cache warming strategies</li>
    <li>Monitor cache hit/miss ratio</li>
    <li>Use Redis for production environments</li>
</ul>

<h3>Kết luận</h3>
<p>Caching là công cụ mạnh mẽ để cải thiện performance. Hãy sử dụng đúng cách và monitoring thường xuyên để đạt hiệu quả tốt nhất.</p>
HTML;
    }
    
    private function getLaravelApiContent(): string
    {
        return <<<HTML
<h2>Xây dựng RESTful API với Laravel</h2>
<p>RESTful API là tiêu chuẩn trong việc xây dựng web services. Laravel cung cấp nhiều công cụ mạnh mẽ để xây dựng API một cách nhanh chóng và hiệu quả.</p>

<h3>1. API Routes</h3>
<pre><code class="language-php">// routes/api.php
Route::apiResource('users', UserController::class);

Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});</code></pre>

<h3>2. API Resources</h3>
<pre><code class="language-php">// Create resource
php artisan make:resource UserResource

// UserResource.php
class UserResource extends JsonResource
{
    public function toArray(\$request)
    {
        return [
            'id' => \$this->id,
            'name' => \$this->name,
            'email' => \$this->email,
            'created_at' => \$this->created_at->toDateTimeString(),
        ];
    }
}</code></pre>

<h3>3. API Authentication với Sanctum</h3>
<pre><code class="language-php">// Install Sanctum
composer require laravel/sanctum

// User model
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}

// Generate token
\$token = \$user->createToken('api-token')->plainTextToken;</code></pre>

<h3>4. Request Validation</h3>
<pre><code class="language-php">public function store(Request \$request)
{
    \$validated = \$request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ]);
    
    // Create user
    \$user = User::create(\$validated);
    
    return new UserResource(\$user);
}</code></pre>

<h3>5. Error Handling</h3>
<pre><code class="language-php">// app/Exceptions/Handler.php
public function render(\$request, Throwable \$exception)
{
    if (\$request->is('api/*')) {
        if (\$exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }
        
        if (\$exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => \$exception->errors()
            ], 422);
        }
    }
    
    return parent::render(\$request, \$exception);
}</code></pre>

<h3>Best Practices</h3>
<ul>
    <li>Sử dụng proper HTTP status codes</li>
    <li>Implement versioning từ đầu</li>
    <li>Use API Resources for consistent responses</li>
    <li>Implement rate limiting</li>
    <li>Document API với tools như Swagger</li>
</ul>
HTML;
    }
    
    private function getAwsLambdaContent(): string
    {
        return <<<HTML
<h2>AWS Lambda và Serverless cho PHP Developers</h2>
<p>Serverless architecture đang trở thành trend trong cloud computing. AWS Lambda cho phép chạy code mà không cần quản lý servers.</p>

<h3>1. Lambda Function với PHP</h3>
<pre><code class="language-php">// index.php
function handler(\$event, \$context) {
    return [
        'statusCode' => 200,
        'body' => json_encode([
            'message' => 'Hello from Lambda!',
            'input' => \$event
        ])
    ];
}</code></pre>

<h3>2. Deploy với Serverless Framework</h3>
<pre><code class="language-yaml"># serverless.yml
service: php-lambda

provider:
  name: aws
  runtime: provided.al2
  region: ap-southeast-1

functions:
  hello:
    handler: index.handler
    layers:
      - arn:aws:lambda:region:account:layer:php-80:1
    events:
      - http:
          path: hello
          method: get</code></pre>

<h3>3. Use Cases phù hợp</h3>
<ul>
    <li>Image/Video processing</li>
    <li>Webhook handlers</li>
    <li>Scheduled tasks (Cron jobs)</li>
    <li>API Gateway backends</li>
    <li>Real-time file processing</li>
</ul>

<h3>4. Cost Optimization</h3>
<p>Lambda tính phí theo số request và thời gian thực thi. Tips để optimize:</p>
<ul>
    <li>Minimize cold starts</li>
    <li>Optimize memory allocation</li>
    <li>Use Lambda layers for dependencies</li>
    <li>Implement caching strategies</li>
</ul>
HTML;
    }
    
    private function getDockerContent(): string
    {
        return <<<HTML
<h2>Docker cho Laravel Developers</h2>
<p>Docker giúp đảm bảo consistency giữa development và production environments. Hướng dẫn này sẽ giúp bạn dockerize Laravel application.</p>

<h3>1. Dockerfile cho Laravel</h3>
<pre><code class="language-dockerfile"># Dockerfile
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \\
    git \\
    curl \\
    libpng-dev \\
    libonig-dev \\
    libxml2-dev \\
    zip \\
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000</code></pre>

<h3>2. Docker Compose Setup</h3>
<pre><code class="language-yaml"># docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel-app
    volumes:
      - ./:/var/www
    networks:
      - laravel

  nginx:
    image: nginx:alpine
    container_name: laravel-nginx
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    networks:
      - laravel

  mysql:
    image: mysql:8.0
    container_name: laravel-mysql
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_ROOT_PASSWORD: secret
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - laravel

  redis:
    image: redis:alpine
    container_name: laravel-redis
    ports:
      - "6379:6379"
    networks:
      - laravel

networks:
  laravel:
    driver: bridge

volumes:
  mysql_data:
    driver: local</code></pre>

<h3>3. Production Best Practices</h3>
<ul>
    <li>Multi-stage builds để giảm image size</li>
    <li>Use Alpine Linux cho smaller footprint</li>
    <li>Implement health checks</li>
    <li>Use secrets management</li>
    <li>Enable OPcache cho production</li>
</ul>
HTML;
    }
    
    private function getVueContent(): string
    {
        return <<<HTML
<h2>Vue.js 3 Composition API</h2>
<p>Vue 3 giới thiệu Composition API - một cách mới để organize logic trong components. Hãy cùng tìm hiểu cách sử dụng.</p>

<h3>1. Setup Function</h3>
<pre><code class="language-javascript">// Composition API
import { ref, computed, onMounted } from 'vue'

export default {
  setup() {
    // Reactive state
    const count = ref(0)
    const name = ref('Vue 3')
    
    // Computed property
    const doubleCount = computed(() => count.value * 2)
    
    // Methods
    const increment = () => {
      count.value++
    }
    
    // Lifecycle
    onMounted(() => {
      console.log('Component mounted!')
    })
    
    // Return cho template
    return {
      count,
      name,
      doubleCount,
      increment
    }
  }
}</code></pre>

<h3>2. Reactive vs Ref</h3>
<pre><code class="language-javascript">import { ref, reactive } from 'vue'

// ref - cho primitive values
const count = ref(0)
console.log(count.value) // 0

// reactive - cho objects
const state = reactive({
  user: {
    name: 'John',
    age: 30
  }
})
console.log(state.user.name) // John</code></pre>

<h3>3. Composables</h3>
<pre><code class="language-javascript">// useCounter.js
import { ref, computed } from 'vue'

export function useCounter() {
  const count = ref(0)
  const doubleCount = computed(() => count.value * 2)
  
  function increment() {
    count.value++
  }
  
  function decrement() {
    count.value--
  }
  
  return {
    count,
    doubleCount,
    increment,
    decrement
  }
}

// Component
import { useCounter } from './useCounter'

export default {
  setup() {
    const { count, increment } = useCounter()
    
    return {
      count,
      increment
    }
  }
}</code></pre>

<h3>Advantages của Composition API</h3>
<ul>
    <li>Better TypeScript support</li>
    <li>Logic reuse và composition</li>
    <li>More flexible code organization</li>
    <li>Better IDE support</li>
</ul>
HTML;
    }
    
    private function getDatabaseContent(): string
    {
        return <<<HTML
<h2>Database Optimization: Indexing trong MySQL</h2>
<p>Indexing là yếu tố quan trọng nhất trong database optimization. Hiểu và sử dụng đúng indexes có thể cải thiện performance lên đến hàng trăm lần.</p>

<h3>1. Types of Indexes</h3>
<ul>
    <li><strong>PRIMARY KEY</strong>: Unique identifier cho mỗi row</li>
    <li><strong>UNIQUE</strong>: Đảm bảo unique values</li>
    <li><strong>INDEX</strong>: Regular index cho faster lookups</li>
    <li><strong>FULLTEXT</strong>: Cho text searching</li>
    <li><strong>SPATIAL</strong>: Cho geographic data</li>
</ul>

<h3>2. Creating Indexes</h3>
<pre><code class="language-sql">-- Single column index
CREATE INDEX idx_email ON users(email);

-- Composite index
CREATE INDEX idx_name_created ON users(last_name, first_name, created_at);

-- Unique index
CREATE UNIQUE INDEX idx_unique_email ON users(email);

-- Fulltext index
CREATE FULLTEXT INDEX idx_fulltext_content ON posts(title, content);</code></pre>

<h3>3. Using EXPLAIN</h3>
<pre><code class="language-sql">EXPLAIN SELECT * FROM users WHERE email = 'user@example.com';

-- Output shows:
-- type: const (very fast)
-- possible_keys: idx_email
-- key: idx_email
-- rows: 1</code></pre>

<h3>4. Index Best Practices</h3>
<ul>
    <li>Index columns used in WHERE, JOIN, ORDER BY</li>
    <li>Put most selective columns first in composite indexes</li>
    <li>Don't over-index - indexes slow down writes</li>
    <li>Monitor slow query log</li>
    <li>Use covering indexes when possible</li>
</ul>

<h3>5. Common Mistakes</h3>
<ul>
    <li>Not using indexes on foreign keys</li>
    <li>Using functions on indexed columns in WHERE</li>
    <li>Wrong column order in composite indexes</li>
    <li>Too many indexes on frequently updated tables</li>
</ul>
HTML;
    }
    
    private function getPythonContent(): string
    {
        return <<<HTML
<h2>Python cho Web Developers: Django vs Flask</h2>
<p>Python có hai framework phổ biến cho web development: Django và Flask. Mỗi framework có ưu điểm riêng và phù hợp cho các use cases khác nhau.</p>

<h3>Django - The Web Framework for Perfectionists</h3>
<pre><code class="language-python"># Django view
from django.shortcuts import render
from django.http import JsonResponse
from .models import Post

def post_list(request):
    posts = Post.objects.all()
    return render(request, 'posts/list.html', {'posts': posts})

def api_posts(request):
    posts = Post.objects.values('id', 'title', 'created_at')
    return JsonResponse(list(posts), safe=False)</code></pre>

<h3>Flask - Micro Framework</h3>
<pre><code class="language-python"># Flask app
from flask import Flask, render_template, jsonify
from flask_sqlalchemy import SQLAlchemy

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///blog.db'
db = SQLAlchemy(app)

@app.route('/')
def index():
    posts = Post.query.all()
    return render_template('index.html', posts=posts)

@app.route('/api/posts')
def api_posts():
    posts = Post.query.all()
    return jsonify([p.to_dict() for p in posts])</code></pre>

<h3>So sánh Django vs Flask</h3>
<table>
    <tr>
        <th>Feature</th>
        <th>Django</th>
        <th>Flask</th>
    </tr>
    <tr>
        <td>Philosophy</td>
        <td>Batteries included</td>
        <td>Minimalist</td>
    </tr>
    <tr>
        <td>Admin Interface</td>
        <td>Built-in</td>
        <td>Third-party</td>
    </tr>
    <tr>
        <td>ORM</td>
        <td>Django ORM</td>
        <td>SQLAlchemy (optional)</td>
    </tr>
    <tr>
        <td>Learning Curve</td>
        <td>Steeper</td>
        <td>Gentler</td>
    </tr>
</table>

<h3>Khi nào dùng Django?</h3>
<ul>
    <li>Large-scale applications</li>
    <li>Need built-in admin interface</li>
    <li>Rapid development với nhiều features</li>
    <li>Team collaboration</li>
</ul>

<h3>Khi nào dùng Flask?</h3>
<ul>
    <li>Microservices</li>
    <li>APIs</li>
    <li>Small to medium projects</li>
    <li>Need flexibility và control</li>
</ul>
HTML;
    }
    
    private function getCiCdContent(): string
    {
        return <<<HTML
<h2>CI/CD với GitHub Actions cho Laravel</h2>
<p>GitHub Actions là công cụ CI/CD mạnh mẽ, tích hợp sẵn với GitHub. Hướng dẫn này sẽ giúp bạn setup complete pipeline cho Laravel.</p>

<h3>1. Basic Workflow</h3>
<pre><code class="language-yaml"># .github/workflows/laravel.yml
name: Laravel CI/CD

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, dom, fileinfo, mysql
        coverage: xdebug
    
    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"
    
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
    
    - name: Generate key
      run: php artisan key:generate
    
    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache
    
    - name: Run Migrations
      env:
        DB_CONNECTION: mysql
        DB_HOST: 127.0.0.1
        DB_PORT: 3306
        DB_DATABASE: testing
        DB_USERNAME: root
        DB_PASSWORD: password
      run: php artisan migrate
    
    - name: Execute tests
      run: vendor/bin/phpunit --coverage-text</code></pre>

<h3>2. Deploy to Production</h3>
<pre><code class="language-yaml">deploy:
  needs: test
  runs-on: ubuntu-latest
  if: github.ref == 'refs/heads/main'
  
  steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: \${{ secrets.HOST }}
        username: \${{ secrets.USERNAME }}
        key: \${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/laravel-app
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan queue:restart</code></pre>

<h3>3. Advanced Features</h3>
<ul>
    <li>Run Laravel Dusk tests</li>
    <li>Build và push Docker images</li>
    <li>Deploy to AWS/DigitalOcean</li>
    <li>Send notifications to Slack</li>
    <li>Run security audits</li>
</ul>
HTML;
    }
    
    private function getMachineLearningContent(): string
    {
        return <<<HTML
<h2>Machine Learning với Python và TensorFlow</h2>
<p>Machine Learning đang thay đổi cách chúng ta xây dựng applications. Hãy bắt đầu với TensorFlow và Python.</p>

<h3>1. Setup Environment</h3>
<pre><code class="language-bash"># Create virtual environment
python -m venv ml_env

# Activate
source ml_env/bin/activate  # Linux/Mac
ml_env\\Scripts\\activate  # Windows

# Install packages
pip install tensorflow numpy pandas matplotlib scikit-learn</code></pre>

<h3>2. Simple Neural Network</h3>
<pre><code class="language-python">import tensorflow as tf
from tensorflow import keras
import numpy as np

# Load dataset
(x_train, y_train), (x_test, y_test) = keras.datasets.mnist.load_data()

# Preprocess
x_train = x_train / 255.0
x_test = x_test / 255.0

# Build model
model = keras.Sequential([
    keras.layers.Flatten(input_shape=(28, 28)),
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dropout(0.2),
    keras.layers.Dense(10, activation='softmax')
])

# Compile
model.compile(optimizer='adam',
              loss='sparse_categorical_crossentropy',
              metrics=['accuracy'])

# Train
model.fit(x_train, y_train, epochs=5)

# Evaluate
test_loss, test_acc = model.evaluate(x_test, y_test)
print(f'Test accuracy: {test_acc}')</code></pre>

<h3>3. Practical Applications</h3>
<ul>
    <li>Image Classification</li>
    <li>Text Sentiment Analysis</li>
    <li>Recommendation Systems</li>
    <li>Fraud Detection</li>
    <li>Price Prediction</li>
</ul>

<h3>4. Resources để học thêm</h3>
<ul>
    <li>TensorFlow Official Tutorials</li>
    <li>Coursera - Andrew Ng's Course</li>
    <li>Fast.ai Practical Deep Learning</li>
    <li>Papers with Code</li>
</ul>
HTML;
    }
    
    private function getCleanCodeContent(): string
    {
        return <<<HTML
<h2>Clean Code trong PHP</h2>
<p>Clean code không chỉ là code chạy được, mà còn phải dễ đọc, dễ maintain và dễ mở rộng. Hãy cùng tìm hiểu các nguyên tắc quan trọng.</p>

<h3>1. Meaningful Names</h3>
<pre><code class="language-php">// Bad
\$d = 30; // days
\$u = getUsers();

// Good
\$daysInMonth = 30;
\$activeUsers = getActiveUsers();</code></pre>

<h3>2. Functions Should Do One Thing</h3>
<pre><code class="language-php">// Bad
function createUserAndSendEmail(\$data) {
    \$user = User::create(\$data);
    Mail::to(\$user->email)->send(new WelcomeMail(\$user));
    Log::info('User created: ' . \$user->id);
    return \$user;
}

// Good
function createUser(\$data) {
    return User::create(\$data);
}

function sendWelcomeEmail(User \$user) {
    Mail::to(\$user->email)->send(new WelcomeMail(\$user));
}

function logUserCreation(User \$user) {
    Log::info('User created: ' . \$user->id);
}

// Usage
\$user = createUser(\$data);
sendWelcomeEmail(\$user);
logUserCreation(\$user);</code></pre>

<h3>3. DRY - Don't Repeat Yourself</h3>
<pre><code class="language-php">// Bad
\$priceWithTax = \$price + (\$price * 0.1);
\$shippingWithTax = \$shipping + (\$shipping * 0.1);

// Good
function addTax(\$amount, \$taxRate = 0.1) {
    return \$amount + (\$amount * \$taxRate);
}

\$priceWithTax = addTax(\$price);
\$shippingWithTax = addTax(\$shipping);</code></pre>

<h3>4. SOLID Principles</h3>
<p><strong>Single Responsibility:</strong> Mỗi class chỉ có một lý do để thay đổi</p>
<p><strong>Open/Closed:</strong> Open for extension, closed for modification</p>
<p><strong>Liskov Substitution:</strong> Subclasses phải thay thế được parent class</p>
<p><strong>Interface Segregation:</strong> Nhiều interface nhỏ tốt hơn một interface lớn</p>
<p><strong>Dependency Inversion:</strong> Depend on abstractions, not concretions</p>

<h3>5. Comments</h3>
<pre><code class="language-php">// Bad - obvious comment
// Increment i by 1
\$i++;

// Good - explains why
// We need to skip the header row
\$startIndex = 1;</code></pre>

<h3>Best Practices</h3>
<ul>
    <li>Write code for humans, not computers</li>
    <li>Refactor regularly</li>
    <li>Keep functions and classes small</li>
    <li>Use descriptive variable names</li>
    <li>Follow PSR standards</li>
</ul>
HTML;
    }
    
    private function generateRandomContent($faker): string
    {
        $sections = rand(3, 6);
        $content = '';
        
        for ($i = 0; $i < $sections; $i++) {
            $content .= '<h2>' . $faker->sentence(rand(3, 6)) . '</h2>';
            $content .= '<p>' . $faker->paragraph(rand(5, 8)) . '</p>';
            
            if (rand(0, 1)) {
                $content .= '<ul>';
                for ($j = 0; $j < rand(3, 6); $j++) {
                    $content .= '<li>' . $faker->sentence() . '</li>';
                }
                $content .= '</ul>';
            }
            
            if (rand(0, 1)) {
                $content .= '<blockquote>' . $faker->paragraph(3) . '</blockquote>';
            }
            
            $content .= '<p>' . $faker->paragraph(rand(4, 7)) . '</p>';
        }
        
        return $content;
    }
}