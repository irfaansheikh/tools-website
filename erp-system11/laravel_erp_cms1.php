<?php

// ===== COMPOSER.JSON =====
/*
{
    "name": "erp/cms-system",
    "type": "project",
    "description": "Complete ERP/CMS System",
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8",
        "spatie/laravel-permission": "^5.10",
        "barryvdh/laravel-dompdf": "^2.0",
        "maatwebsite/excel": "^3.1"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ]
    }
}
*/

// ===== ROUTES/WEB.PHP =====
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    FinancialController,
    SalesController,
    InventoryController,
    PurchasingController,
    HrmController,
    CustomerController,
    ReportController,
    SettingsController,
    AuthController
};

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Financial Routes
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::resource('invoices', FinancialController::class);
        Route::get('/accounting', [FinancialController::class, 'accounting'])->name('accounting');
        Route::get('/budgeting', [FinancialController::class, 'budgeting'])->name('budgeting');
        Route::get('/expenses', [FinancialController::class, 'expenses'])->name('expenses');
        Route::get('/payments', [FinancialController::class, 'payments'])->name('payments');
    });
    
    // Sales & Inventory Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::resource('orders', SalesController::class);
        Route::get('/tracking', [SalesController::class, 'tracking'])->name('tracking');
        Route::get('/returns', [SalesController::class, 'returns'])->name('returns');
    });
    
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::resource('products', InventoryController::class);
        Route::get('/categories', [InventoryController::class, 'categories'])->name('categories');
        Route::get('/alerts', [InventoryController::class, 'alerts'])->name('alerts');
    });
    
    // Purchasing Routes
    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/', [PurchasingController::class, 'index'])->name('index');
        Route::resource('orders', PurchasingController::class);
        Route::get('/bills', [PurchasingController::class, 'bills'])->name('bills');
        Route::get('/suppliers', [PurchasingController::class, 'suppliers'])->name('suppliers');
    });
    
    // HRM Routes
    Route::prefix('hrm')->name('hrm.')->group(function () {
        Route::get('/', [HrmController::class, 'index'])->name('index');
        Route::resource('employees', HrmController::class);
        Route::get('/payroll', [HrmController::class, 'payroll'])->name('payroll');
        Route::get('/leaves', [HrmController::class, 'leaves'])->name('leaves');
    });
    
    // Customer Routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::resource('customers', CustomerController::class);
        Route::get('/quotations', [CustomerController::class, 'quotations'])->name('quotations');
    });
    
    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/export/{type}/{format}', [ReportController::class, 'export'])->name('export');
    });
    
    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/users', [SettingsController::class, 'users'])->name('users');
        Route::get('/roles', [SettingsController::class, 'roles'])->name('roles');
        Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
    });
});

// ===== APP/HTTP/CONTROLLERS/DASHBOARDCONTROLLER.PHP =====
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Invoice, Product, Customer, Employee, Sale};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_sales' => Sale::sum('total_amount'),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'total_employees' => Employee::count(),
            'recent_sales' => Sale::with('customer')->latest()->take(5)->get(),
            'low_stock_products' => Product::where('stock_quantity', '<=', 'min_stock_level')->get(),
            'monthly_sales' => $this->getMonthlySales(),
            'category_sales' => $this->getCategorySales()
        ];
        
        return view('dashboard.index', compact('data'));
    }
    
    private function getMonthlySales()
    {
        return Sale::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                  ->whereYear('created_at', date('Y'))
                  ->groupBy('month')
                  ->pluck('total', 'month')
                  ->toArray();
    }
    
    private function getCategorySales()
    {
        return Product::join('sales_items', 'products.id', '=', 'sales_items.product_id')
                     ->join('categories', 'products.category_id', '=', 'categories.id')
                     ->selectRaw('categories.name, SUM(sales_items.quantity * sales_items.price) as total')
                     ->groupBy('categories.id', 'categories.name')
                     ->pluck('total', 'name')
                     ->toArray();
    }
}

// ===== APP/HTTP/CONTROLLERS/FINANCIALCONTROLLER.PHP =====
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Invoice, Expense, Payment, Account};
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(10);
        return view('financial.index', compact('invoices'));
    }
    
    public function create()
    {
        return view('financial.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);
        
        $invoice = Invoice::create($validated);
        
        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }
        
        return redirect()->route('financial.index')->with('success', 'Invoice created successfully');
    }
    
    public function accounting()
    {
        $data = [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'total_expenses' => Expense::sum('amount'),
            'accounts_receivable' => Invoice::where('status', 'pending')->sum('total_amount'),
            'accounts_payable' => Payment::where('status', 'pending')->sum('amount'),
            'profit_loss' => $this->generateProfitLoss(),
            'balance_sheet' => $this->generateBalanceSheet()
        ];
        
        return view('financial.accounting', compact('data'));
    }
    
    public function budgeting()
    {
        return view('financial.budgeting');
    }
    
    public function expenses()
    {
        $expenses = Expense::latest()->paginate(10);
        return view('financial.expenses', compact('expenses'));
    }
    
    public function payments()
    {
        $payments = Payment::with('invoice')->latest()->paginate(10);
        return view('financial.payments', compact('payments'));
    }
    
    private function generateProfitLoss()
    {
        return [
            'revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'expenses' => Expense::sum('amount'),
            'gross_profit' => Invoice::where('status', 'paid')->sum('total_amount') - Expense::sum('amount')
        ];
    }
    
    private function generateBalanceSheet()
    {
        return [
            'assets' => [
                'cash' => Account::where('type', 'cash')->sum('balance'),
                'accounts_receivable' => Invoice::where('status', 'pending')->sum('total_amount'),
                'inventory' => Product::sum(\DB::raw('stock_quantity * unit_cost'))
            ],
            'liabilities' => [
                'accounts_payable' => Payment::where('status', 'pending')->sum('amount')
            ]
        ];
    }
}

// ===== APP/HTTP/CONTROLLERS/SALESCONTROLLER.PHP =====
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Sale, Product, Customer, SaleItem};

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }
    
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('stock_quantity', '>', 0)->get();
        return view('sales.create', compact('customers', 'products'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);
        
        $sale = Sale::create([
            'customer_id' => $validated['customer_id'],
            'sale_date' => $validated['sale_date'],
            'total_amount' => 0
        ]);
        
        $total = 0;
        foreach ($validated['items'] as $item) {
            $saleItem = $sale->items()->create($item);
            $total += $item['quantity'] * $item['price'];
            
            // Update inventory
            $product = Product::find($item['product_id']);
            $product->decrement('stock_quantity', $item['quantity']);
        }
        
        $sale->update(['total_amount' => $total]);
        
        return redirect()->route('sales.index')->with('success', 'Sale created successfully');
    }
    
    public function tracking()
    {
        $sales = Sale::with(['customer', 'items.product'])->latest()->get();
        return view('sales.tracking', compact('sales'));
    }
    
    public function returns()
    {
        $returns = Sale::where('status', 'returned')->with('customer')->get();
        return view('sales.returns', compact('returns'));
    }
}

// ===== APP/HTTP/CONTROLLERS/INVENTORYCONTROLLER.PHP =====
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product, Category};

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        $lowStockCount = Product::whereRaw('stock_quantity <= min_stock_level')->count();
        return view('inventory.index', compact('products', 'lowStockCount'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('inventory.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        Product::create($validated);
        
        return redirect()->route('inventory.index')->with('success', 'Product created successfully');
    }
    
    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('inventory.categories', compact('categories'));
    }
    
    public function alerts()
    {
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')
                                  ->with('category')
                                  ->get();
        return view('inventory.alerts', compact('lowStockProducts'));
    }
}

// ===== APP/MODELS/USER.PHP =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

// ===== APP/MODELS/CUSTOMER.PHP =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'state', 'zip_code', 'country'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

// ===== APP/MODELS/PRODUCT.PHP =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'description', 'category_id', 'unit_price', 'unit_cost',
        'stock_quantity', 'min_stock_level', 'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }
}

// ===== APP/MODELS/SALE.PHP =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'sale_date', 'total_amount', 'status', 'notes'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}

// ===== APP/MODELS/INVOICE.PHP =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'invoice_number', 'invoice_date', 'due_date', 
        'total_amount', 'status', 'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}

// ===== DATABASE/MIGRATIONS/CREATE_USERS_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

// ===== DATABASE/MIGRATIONS/CREATE_CUSTOMERS_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

// ===== DATABASE/MIGRATIONS/CREATE_CATEGORIES_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

// ===== DATABASE/MIGRATIONS/CREATE_PRODUCTS_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

// ===== DATABASE/MIGRATIONS/CREATE_SALES_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->date('sale_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

// ===== DATABASE/MIGRATIONS/CREATE_INVOICES_TABLE.PHP =====
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

// ===== DATABASE/SEEDERS/DATABASESEEDER.PHP =====
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Customer, Category, Product, Sale, Invoice};
use Spatie\Permission\Models\{Role, Permission};
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles and permissions
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $employeeRole = Role::create(['name' => 'employee']);
        
        $permissions = [
            'view-dashboard', 'manage-users', 'manage-customers', 'manage-products',
            'manage-sales', 'manage-inventory', 'manage-finances', 'view-reports'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        $adminRole->givePermissionTo(Permission::all());
        $managerRole->givePermissionTo(['view-dashboard', 'manage-customers', 'manage-products', 'manage-sales', 'view-reports']);
        $employeeRole->givePermissionTo(['view-dashboard', 'manage-customers', 'manage-sales']);
        
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@erp.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');
        
        // Create sample customers
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '123-456-7890'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '987-654-3210'],
            ['name' => 'ABC Corp', 'email' => 'contact@abc.com', 'phone' => '555-123-4567'],
        ];
        
        foreach ($customers as $customer) {
            Customer::create($customer);
        }
        
        // Create sample categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Apparel and fashion items'],
            ['name' => 'Books', 'description' => 'Books and publications'],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        // Create sample products
        $products = [
            ['name' => 'Laptop', 'sku' => 'LAP001', 'category_id' => 1, 'unit_price' => 999.99, 'unit_cost' => 750.00, 'stock_quantity' => 50, 'min_stock_level' => 10],
            ['name' => 'Smartphone', 'sku' => 'PHN001', 'category_id' => 1, 'unit_price' => 699.99, 'unit_cost' => 500.00, 'stock_quantity' => 100, 'min_stock_level' => 20],
            ['name' => 'T-Shirt', 'sku' => 'TSH001', 'category_id' => 2, 'unit_price' => 19.99, 'unit_cost' => 10.00, 'stock_quantity' => 200, 'min_stock_level' => 50],
            ['name' => 'Programming Book', 'sku' => 'BK001', 'category_id' => 3, 'unit_price' => 49.99, 'unit_cost' => 25.00, 'stock_quantity' => 30, 'min_stock_level' => 5],
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }
        
        // Create sample sales
        Sale::create([
            'customer_id' => 1,
            'sale_date' => now(),
            'total_amount' => 1049.98,
            'status' => 'completed'
        ]);
        
        // Create sample invoices
        Invoice::create([
            'customer_id' => 2,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 699.99,
            'status' => 'sent'
        ]);
    }
}