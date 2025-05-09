<?php

namespace Database\Seeders;

use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductImportSeeder extends Seeder
{
    /**
     * Шляхи до директорій зображень
     */
    private $sourceImagesDir;
    private $targetImagesDir;
    private $categoriesMap = [];
    private $usedImages = [];
    private $availableImages = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // В Docker контейнері шляхи інші, визначаємо базову директорію
        $basePath = '/var/www';
        if (!is_dir($basePath)) {
            $basePath = base_path();
        }

        // Зберігаємо шляхи до директорій
        $this->sourceImagesDir = $basePath . '/database/seeders/images/products';
        $this->targetImagesDir = $basePath . '/storage/app/public/images/products';
        
        // Створюємо цільову директорію, якщо вона не існує
        if (!File::isDirectory($this->targetImagesDir)) {
            File::makeDirectory($this->targetImagesDir, 0755, true);
            $this->command->info("Створено директорію: {$this->targetImagesDir}");
        }

        // Завантажуємо доступні зображення
        $this->loadAvailableImages();
        
        // Завантажуємо категорії для швидкого доступу
        $this->loadCategoriesMap();

        // Шлях до CSV-файлу
        $csvPath = $basePath . '/database/seeders/fixture/products.csv';
        
        if (!File::exists($csvPath)) {
            $this->command->error("CSV-файл не знайдено: {$csvPath}");
            return;
        }

        // Завантаження та обробка CSV-файлу
        $this->command->info('Імпортування продуктів з CSV-файлу...');
        $this->importProductsFromCsv($csvPath);
        
        // Копіюємо використані зображення для продуктів
        $this->copyProductImages();
        
        $this->command->info('Імпорт продуктів завершено!');
    }

    /**
     * Завантажує список доступних зображень
     */
    private function loadAvailableImages(): void
    {
        if (File::isDirectory($this->sourceImagesDir)) {
            $files = File::files($this->sourceImagesDir);
            foreach ($files as $file) {
                $this->availableImages[] = $file->getFilename();
            }
            $this->command->info("Знайдено " . count($this->availableImages) . " зображень для продуктів");
        } else {
            $this->command->error("Директорія з зображеннями не знайдена: {$this->sourceImagesDir}");
        }
    }

    /**
     * Завантажує категорії і зберігає їх в карту для швидкого доступу
     */
    private function loadCategoriesMap(): void
    {
        $categories = CatalogCategory::all();
        foreach ($categories as $category) {
            $this->categoriesMap[$category->name] = $category;
        }
    }

    /**
     * Імпортує продукти з CSV-файлу
     */
    private function importProductsFromCsv(string $csvPath): void
    {
        $handle = fopen($csvPath, 'r');
        
        // Читаємо заголовки
        $headers = fgetcsv($handle);
        
        $count = 0;
        
        // Читаємо рядки з даними
        while (($data = fgetcsv($handle)) !== false) {
            $productData = array_combine($headers, $data);
            
            // Визначаємо тип продукту і підбираємо зображення
            $nutType = $productData['nut_type'] ?? null;
            $categoryName = $productData['category'];
            
            // Підбираємо зображення на основі типу продукту і категорії
            $imagePath = $this->selectImageForProduct($nutType, $categoryName);
            
            // Конвертуємо стрічкові "True"/"False" в булеві значення
            $booleanFields = ['salted', 'roasted', 'gluten_free', 'organic', 'is_in_stock'];
            foreach ($booleanFields as $field) {
                if (isset($productData[$field])) {
                    $productData[$field] = filter_var($productData[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }
            
            // Конвертуємо стрічкове представлення масиву в масив для gallery
            $gallery = [];
            if (!empty($productData['gallery']) && $productData['gallery'] !== "[]") {
                $galleryString = str_replace(["[", "]", "'"], ["", "", "\""], $productData['gallery']);
                $links = explode(',', $galleryString);
                
                // Додаємо до 3 унікальних зображень для галереї
                $usedGalleryImages = [];
                $galleryCount = 0;
                
                foreach ($this->availableImages as $img) {
                    if ($img !== $imagePath && $galleryCount < 3) {
                        // Перевіряємо, чи зображення підходить для цього типу продукту
                        if ($this->isImageSuitableForProduct($img, $nutType, $categoryName)) {
                            $gallery[] = "images/products/{$img}";
                            $usedGalleryImages[$img] = $this->sourceImagesDir . '/' . $img;
                            $galleryCount++;
                        }
                    }
                }
                
                // Додаємо використані зображення галереї до списку для копіювання
                foreach ($usedGalleryImages as $filename => $path) {
                    $this->usedImages[$filename] = $path;
                }
            }
            
            // Створюємо або оновлюємо продукт
            $product = CatalogProduct::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'price' => (float)$productData['price'],
                    'base_currency' => $productData['base_currency'],
                    'qty' => (int)$productData['qty'],
                    'is_in_stock' => $productData['is_in_stock'],
                    'visibility' => (int)$productData['visibility'],
                    'status' => (int)$productData['status'],
                    'nut_type' => $productData['nut_type'],
                    'sweetness_level' => !empty($productData['sweetness_level']) ? (int)$productData['sweetness_level'] : null,
                    'cocoa_pct' => !empty($productData['cocoa_pct']) ? (float)$productData['cocoa_pct'] : null,
                    'salted' => $productData['salted'],
                    'roasted' => $productData['roasted'],
                    'gluten_free' => $productData['gluten_free'],
                    'organic' => $productData['organic'],
                    'origin_country' => $productData['origin_country'],
                    'weight_g' => !empty($productData['weight_g']) ? (int)$productData['weight_g'] : null,
                    'expiry_date' => !empty($productData['expiry_date']) ? $productData['expiry_date'] : null,
                    'image' => "images/products/{$imagePath}",
                    'image_alt' => $productData['image_alt'],
                    'gallery' => $gallery
                ]
            );
            
            // Зв'язуємо продукт з категорією
            if (isset($this->categoriesMap[$categoryName])) {
                $product->categories()->syncWithoutDetaching([$this->categoriesMap[$categoryName]->category_id]);
                $this->command->info("Продукт {$product->name} зв'язаний з категорією {$categoryName}");
            }
            
            $count++;
        }
        
        fclose($handle);
        
        $this->command->info("Імпортовано {$count} продуктів.");
    }

    /**
     * Перевіряє, чи підходить зображення для продукту
     */
    private function isImageSuitableForProduct(string $image, ?string $nutType, string $categoryName): bool
    {
        $imageLower = strtolower($image);
        
        if ($nutType) {
            $nutTypeLower = strtolower($nutType);
            if (strpos($imageLower, $nutTypeLower) !== false) {
                return true;
            }
        }
        
        $categoryMap = [
            'Горіхи' => ['nuts', 'nut'],
            'Мигдаль' => ['almond', 'almonds'],
            'Волоські горіхи' => ['walnut', 'walnuts'],
            'Фісташки' => ['pistachio', 'pistachios'],
            'Шоколад' => ['chocolate', 'choco'],
            'Цукерки' => ['candy', 'candies', 'sweets'],
            'Солодощі' => ['sweets', 'sweet', 'candy', 'candies']
        ];
        
        if (isset($categoryMap[$categoryName])) {
            foreach ($categoryMap[$categoryName] as $keyword) {
                if (strpos($imageLower, $keyword) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Вибирає відповідне зображення для продукту на основі типу і категорії
     */
    private function selectImageForProduct(?string $nutType, string $categoryName): string
    {
        // Якщо немає доступних зображень, повертаємо пусте значення
        if (empty($this->availableImages)) {
            return '';
        }
        
        // Шукаємо відповідні зображення за типом продукту
        $suitableImages = [];
        foreach ($this->availableImages as $image) {
            if ($this->isImageSuitableForProduct($image, $nutType, $categoryName)) {
                $suitableImages[] = $image;
            }
        }
        
        // Якщо знайдено відповідні зображення, вибираємо одне випадково
        if (!empty($suitableImages)) {
            $selectedImage = $suitableImages[array_rand($suitableImages)];
        } else {
            // Якщо відповідних зображень немає, беремо випадкове з доступних
            $selectedImage = $this->availableImages[array_rand($this->availableImages)];
        }
        
        // Запам'ятовуємо для копіювання
        $this->usedImages[$selectedImage] = $this->sourceImagesDir . '/' . $selectedImage;
        
        return $selectedImage;
    }

    /**
     * Копіює використані зображення з директорії категорій у директорію продуктів
     */
    private function copyProductImages(): void
    {
        $this->command->info('Копіювання зображень продуктів...');
        
        foreach ($this->usedImages as $filename => $sourcePath) {
            $targetPath = $this->targetImagesDir . '/' . $filename;
            
            // Копіюємо файл, якщо він не існує або відрізняється
            if (!File::exists($targetPath) || md5_file($sourcePath) !== md5_file($targetPath)) {
                File::copy($sourcePath, $targetPath);
                $this->command->info("Скопійовано: {$filename}");
            } else {
                $this->command->info("Файл вже існує: {$filename}");
            }
        }
        
        // Переконуємося, що символічне посилання на сховище існує
        $basePath = '/var/www';
        if (!is_dir($basePath)) {
            $basePath = base_path();
        }
        
        $publicPath = $basePath . '/public';
        if (is_dir($publicPath) && !file_exists($publicPath . '/storage')) {
            $this->command->info('Створення символічного посилання на сховище...');
            symlink($basePath . '/storage/app/public', $publicPath . '/storage');
        }
    }
} 