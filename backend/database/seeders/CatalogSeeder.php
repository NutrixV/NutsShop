<?php

namespace Database\Seeders;

use App\Models\CatalogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Копіювання зображень категорій
        $this->copyImages();
        
        // Оновлюємо або створюємо категорії
        // Замість перевірки на існування, оновлюємо категорії, якщо вони існують
        $this->command->info('Оновлюємо категорії з зображеннями...');
        
        // Створення/оновлення категорій
        $nutsCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Горіхи', 'url_key' => 'nuts'],
            [
                'is_active' => true,
                'position' => 10,
                'image' => 'images/category/nuts-6985014_640.jpg'
            ]
        );
        
        $sweetsCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Солодощі', 'url_key' => 'sweets'],
            [
                'is_active' => true,
                'position' => 20,
                'image' => 'images/category/colorful-1284475_640.jpg'
            ]
        );
        
        // Підкатегорії для горіхів
        $almondCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Мигдаль', 'url_key' => 'almonds'],
            [
                'parent_id' => $nutsCategory->category_id,
                'is_active' => true,
                'position' => 10,
                'image' => 'images/category/almond-83766_640.jpg'
            ]
        );
        
        $walnutCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Волоські горіхи', 'url_key' => 'walnuts'],
            [
                'parent_id' => $nutsCategory->category_id,
                'is_active' => true,
                'position' => 20,
                'image' => 'images/category/walnut-2816935_640.jpg'
            ]
        );
        
        $pistachioCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Фісташки', 'url_key' => 'pistachios'],
            [
                'parent_id' => $nutsCategory->category_id,
                'is_active' => true,
                'position' => 30,
                'image' => 'images/category/pistachio-5167236_640.jpg'
            ]
        );
        
        // Підкатегорії для солодощів
        $chocolateCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Шоколад', 'url_key' => 'chocolate'],
            [
                'parent_id' => $sweetsCategory->category_id,
                'is_active' => true,
                'position' => 10,
                'image' => 'images/category/chocolate-8919274_640.jpg'
            ]
        );
        
        $candyCategory = CatalogCategory::updateOrCreate(
            ['name' => 'Цукерки', 'url_key' => 'candy'],
            [
                'parent_id' => $sweetsCategory->category_id,
                'is_active' => true,
                'position' => 20,
                'image' => 'images/category/ferrero-rocher-1141975_640.jpg'
            ]
        );
        
        $this->command->info('Категорії успішно оновлено!');
    }
    
    /**
     * Копіює зображення з директорії seeders у директорію публічного сховища
     *
     * @return void
     */
    private function copyImages(): void
    {
        $this->command->info('Копіювання зображень категорій...');
        
        // В Docker контейнері шляхи інші, визначаємо базову директорію
        $basePath = '/var/www';
        if (!is_dir($basePath)) {
            $basePath = base_path();
        }
        
        // Створюємо директорію, якщо вона не існує
        $targetDir = $basePath . '/storage/app/public/images/category';
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->command->info("Створено директорію: {$targetDir}");
        }
        
        // Шлях до директорії із seed-зображеннями
        $sourceDir = $basePath . '/database/seeders/images/category';
        
        // Копіюємо всі зображення
        if (File::isDirectory($sourceDir)) {
            $files = File::files($sourceDir);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $targetPath = $targetDir . '/' . $filename;
                
                // Копіюємо файл, якщо він не існує або відрізняється
                if (!File::exists($targetPath) || md5_file($file->getPathname()) !== md5_file($targetPath)) {
                    File::copy($file->getPathname(), $targetPath);
                    $this->command->info("Скопійовано: {$filename}");
                } else {
                    $this->command->info("Файл вже існує: {$filename}");
                }
            }
        } else {
            $this->command->error("Директорія {$sourceDir} не знайдена!");
        }
        
        // Переконуємося, що символічне посилання на сховище існує
        $publicPath = $basePath . '/public';
        if (is_dir($publicPath) && !file_exists($publicPath . '/storage')) {
            $this->command->info('Створення символічного посилання на сховище...');
            symlink($basePath . '/storage/app/public', $publicPath . '/storage');
        }
    }
}
